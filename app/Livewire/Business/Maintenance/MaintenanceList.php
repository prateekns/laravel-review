<?php

namespace App\Livewire\Business\Maintenance;

use App\Models\Business\WorkOrder\WorkOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class MaintenanceList extends Component
{
    use WithPagination;

    /**
     * Search term for filtering maintenance orders
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
     * Get the maintenance orders query for the current business.
     * Applies search filter across multiple columns as per requirements.
     */
    protected function getMaintenanceOrdersQuery(): Builder
    {
        $query = WorkOrder::query()
            ->where('business_id', auth()->guard('business')->user()->business_id)
            ->where('type', WorkOrder::TYPE_MAINTENANCE)
            ->with(['customer', 'template'])
            ->orderBy('created_at', 'desc');

        if (trim($this->search) !== '') {
            $searchTermLike = '%' . trim($this->search) . '%';
            $searchTermId = (int)$this->search;
            $searchTerm = trim($this->search);

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

        return $query;
    }

    /**
     * Render the component.
     */
    public function render(): View
    {
        $maintenanceOrders = $this->getMaintenanceOrdersQuery()->paginate($this->perPage);

        return view('livewire.business.maintenance.list', [
            'maintenanceOrders' => $maintenanceOrders,
        ]);
    }
}
