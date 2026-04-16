<?php

namespace App\Livewire\Business\Customers;

use App\Models\Business\Customer;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class CustomerList extends Component
{
    use WithPagination;

    /**
     * Search query for filtering customers
     *
     * @var string
     */
    public string $search = '';

    /**
     * Number of items to show per page
     *
     * @var int
     */
    protected $perPage = 10;

    /**
     * Listeners for the component
     *
     * @var array
     */
    protected $listeners = ['refreshCustomers' => '$refresh'];

    /**
     * Reset pagination when search is updated
     *
     * @return void
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Clear the search input
     *
     * @return void
     */
    public function clearSearch()
    {
        $this->search = '';
        $this->resetPage();
    }

    /**
     * Get the customers query with search filter
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getCustomersQuery()
    {
        $query = Customer::query()
            ->where('business_id', auth('business')->user()->business_id);

        if (trim($this->search) !== '') {
            $searchTerm = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'like', $searchTerm)
                    ->orWhere('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('commercial_pool_details', 'like', $searchTerm)
                    ->orWhere('email_1', 'like', $searchTerm)
                    ->orWhere('email_2', 'like', $searchTerm);
            });
        }

        return $query->latest();
    }

    /**
     * Render the component
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.business.customers.customer-list', [
            'customers' => $this->getCustomersQuery()->paginate($this->perPage),
        ]);
    }
}
