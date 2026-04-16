<?php

namespace App\Livewire\Business\Technician;

use App\Models\Business\Technician\Technician;
use App\Models\Business\Technician\TechnicianMessage;
use App\Services\Business\BusinessService;
use App\Services\Api\TwilioService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithoutUrlPagination;
use Exception;

class Technicians extends Component
{
    use WithPagination;
    use WithoutUrlPagination;

    public $search = '';
    public $statusFilter = '';
    public $technicianLimitReached = false;
    public $showMessageModal = false;
    public string $selectedMessage = '';
    public array $selectedTechnicians = [];
    public $messages = [];

    /**
     * Number of items to show per page
     */
    protected int $perPage = 10;

    /**
     * @var BusinessService
     */
    protected BusinessService $businessService;

    /**
     * @var TwilioService
     */
    protected TwilioService $twilioService;


    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->businessService = app(BusinessService::class);
        $this->twilioService = app(TwilioService::class);
        $this->user = Auth::guard('business')->user();
    }

    /**
     * Mount the component
     */
    public function mount(): void
    {
        $this->technicianLimitReached = $this->businessService->isTechnicianLimitReached();
        $this->loadMessages();
    }

    /**
     * Load messages from the database
     */
    public function loadMessages(): void
    {
        $this->messages = TechnicianMessage::latest()->get();
    }

    /**
     * Get the validation rules.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'selectedMessage' => ['required'],
        ];
    }

    /**
     * Get the messages for the validation rules.
     *
     * @return array
     */
    protected function messages()
    {
        return [
            'selectedMessage.required' => __('common.auth.required'),
        ];
    }

    /**
     * Send message to technicians
     */
    /**
     * Get formatted phone numbers for selected technicians
     *
     * @return array
     */
    private function getSelectedTechnicianPhones(): array
    {
        return Technician::whereIn('id', $this->selectedTechnicians)
            ->select('isd_code', 'phone')
            ->get()
            ->map(function ($technician) {
                return $technician->isd_code . $technician->phone;
            })
            ->toArray();
    }

    /**
     * Send message to technicians
     */
    public function sendMessage(): void
    {
        $this->validate();
        if ($this->selectedTechnicians && $this->selectedMessage) {
            $phoneNumbers = $this->getSelectedTechnicianPhones();
            foreach ($phoneNumbers as $to) {
                try {
                    $this->twilioService->sendMessage($to, $this->selectedMessage);
                } catch (Exception $e) {
                    Log::error('Failed to send message to technician:', [
                        'phone' => $to,
                        'error' => $e->getMessage()
                    ]);
                }
            }
            $this->selectedMessage = '';
            $this->dispatch('sms-success', [
                'message' => __('business.technician_message_sent')
            ]);
        }
    }

    /**
     * Technician full name attribute
     */
    public function getFullNameAttribute()
    {
        return $this->first_name .' '. $this->last_name;
    }

    /**
     * Updating search
     */
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Updating status filter
     */
    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function openMessageModal(): void
    {
        $this->showMessageModal = true;
    }

    /**
     * Render the component
     */
    public function render(): View
    {
        $business = Auth::guard('business')->user()->business;
        $search = trim($this->search);

        $query = Technician::query()
        ->where('business_id', $business->id);

        if (strlen($search) >= 3) {
            $query->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', '%'.$search.'%')
                        ->orWhere('last_name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%')
                        ->orWhere('phone', 'like', '%'.$search.'%')
                        ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $search . '%'])
                        ->orWhereHas('skills', function ($skillQuery) use ($search) {
                            $skillQuery->where('skill_type', 'like', '%'.$search.'%');
                        });
                });
            });
        }

        $technicians = $query->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.business.technician.technicians', [
            'technicians' => $technicians,
            'technicianLimitReached' => $this->technicianLimitReached,
            'warningMessage' => $this->businessService->getPlanWarningMessage(),
            'pastDue' => $this->businessService->isPastDuePlan(),
        ]);
    }
}
