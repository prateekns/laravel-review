<?php

namespace App\Livewire\Business\WorkOrders;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Contracts\View\View;

class WorkOrdersList extends Component
{
    use WithPagination;

    /**
     * Search term for filtering work orders
     *
     * @var string
     */
    public string $search = '';

    /**
     * Number of items per page
     *
     * @var int
     */
    protected int $perPage = 10;

    /**
     * Querystring parameters
     *
     * @var array
     */
    protected $queryString = [
        'search' => ['except' => '']
    ];

    /**
     * Reset pagination when search is updated
     *
     * @return void
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Get filtered work orders
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getWorkOrders()
    {
        $query = WorkOrder::query()
            ->where('business_id', auth()->guard('business')->user()->business_id)
            ->where('type', WorkOrder::TYPE_WORK_ORDER)
            ->with(['customer', 'template']);

        if ($this->search) {
            $searchTermLike = '%' . $this->search . '%';
            $searchTermId = (int)$this->search;
            $searchTerm = $this->search;
            $query->where(function ($q) use ($searchTermLike, $searchTermId, $searchTerm) {
                $q->where('id', '=', $searchTermId)
                    ->orWhere('name', 'like', $searchTermLike)
                    ->orWhereHas('customer', function ($q) use ($searchTermLike) {
                        $q->where('first_name', 'like', $searchTermLike)
                            ->orWhere('last_name', 'like', $searchTermLike)
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", [$searchTermLike])
                            ->orWhere('commercial_pool_details', 'like', $searchTermLike);
                    })
                    ->orWhere(function ($q) use ($searchTerm) {
                        if (strtolower(trim($searchTerm)) == 'recurring') {
                            $q->where('is_recurring', true);
                        } elseif (strtolower(trim($searchTerm)) == 'non-recurring') {
                            $q->where('is_recurring', false);
                        } elseif (strtolower(trim($searchTerm)) == 'active') {
                            $q->where('is_active', true);
                        } elseif (strtolower(trim($searchTerm)) == 'inactive') {
                            $q->where('is_active', false);
                        }
                    });
            });
        }

        return $query->latest()->paginate($this->perPage);
    }

    /**
     * Render the component
     *
     * @return View
     */
    public function render(): View
    {
        return view('livewire.business.work-orders.list', [
            'workOrders' => $this->getWorkOrders()
        ]);
    }
}
