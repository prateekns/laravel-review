<?php

namespace App\Services\Api;

use App\Models\Business\WorkOrder\WorkOrder;
use App\Models\Business\Technician\Technician;
use App\Models\Business\CompletedJobCustomer;
use App\Models\Business\WorkOrder\WorkOrderChecklistItem;
use App\Models\Business\Chemical\ChemicalLog;
use App\Models\Business\WorkOrder\UsedMaintenanceItem;
use App\Models\Business\WorkOrder\ItemSold;
use App\Traits\ApiResponse;
use App\Constants\ApiStatus;
use App\Enums\WorkOrderStatus;
use App\Exceptions\TechnicianException;
use App\Exceptions\ChemicalException;
use App\Exceptions\JobFetchException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Services\Business\Scheduler\DateTimeService;
use App\Services\Business\Scheduler\TechnicianScheduleService;
use Carbon\Carbon;
use Throwable;

class TechnicianJobService
{
    use ApiResponse;

    public const DEFAULT_TIMEZONE = 'America/New_York';

    public function __construct(private DateTimeService $dateTimeService, private TechnicianScheduleService $technicianScheduleService)
    {
    }

    /**
     * Get paginated jobs for the authenticated technician
     *
     * @return Collection
     */
    public function getJobs()
    {
        try {

            $authUser = auth()->user();
            // Get business timezone
            $businessTimezone = $authUser->business?->timezone ?? self::DEFAULT_TIMEZONE;

            //Get start and end date for jobs
            $nonRecurringStartDate = $startOfWeek = now()->subDays(config('app.past_jobs_days'));
            $nonRecurringEndDate = $endOfWeek = now()->addDays(config('app.future_jobs_days'));


            $technicianId = $authUser->id;
            $businessId = $authUser->business_id;

            if ($authUser && !$authUser->status) {
                $this->invalidateTechnicianTokens($authUser);
                throw new TechnicianException(__('api.technician_not_active'));
            }

            // Get technicians with orders
            $technicians = $this->technicianScheduleService->fetchTechniciansWithOrders(
                $businessId,
                $nonRecurringStartDate,
                $nonRecurringEndDate,
                $startOfWeek,
                $endOfWeek,
                $technicianId
            )->first();

            // Build technician schedule entry
            $techWithData = $this->technicianScheduleService->buildTechnicianScheduleEntry($technicians, $startOfWeek, $businessTimezone, $endOfWeek);

            return $this->prepareJobsCollection($techWithData, $authUser);

        } catch (TechnicianException $e) {
            throw new TechnicianException($e->getMessage());
        } catch (Throwable $th) {
            Log::error('TechnicianJobService.getJobs failed', [
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            throw new JobFetchException(__('api.jobs_fetch_error'));
        }
    }

    /**
     * Prepare jobs collection
     *
     * @param array $jobs
     * @return Collection
     */
    private function prepareJobsCollection(array $techWithData, $authUser): Collection
    {
        // Get workorder ids from schedule with orders
        $jobsCollection = collect();
        $workorderIds = collect($techWithData['schedule'] ?? [])
        ->flatMap(fn ($schedule) => collect($schedule))
        ->pluck('id')
        ->unique()
        ->values()
        ->toArray();

        if (!empty($workorderIds)) {
            $jobs = WorkOrder::with([
                'business.businessChemicals.chemical',
                'business.items',
                'customer',
                'checklist' => function ($query) {
                    $query->whereNull('instance_id');
                }
            ])->whereIn('id', $workorderIds)->get();

            //Add qualified job to collection
            $jobsCollection = $this->addJobToCollection($techWithData, $jobs);
            // Update jobs sync timestamp
            if ($jobsCollection->isNotEmpty()) {
                $authUser->update(['jobs_synced_at' => now()]);
            }
        }

        return $jobsCollection;
    }

    /**
     * Prepare a jobs collection
     * @param $techWithData Technician with it's jobs
     * @param $jobs
     * @return Collection
     */
    public function addJobToCollection($techWithData, $jobs): Collection
    {
        $jobsCollection = collect();

        if ($techWithData && !empty($techWithData['schedule'])) {
            foreach ($techWithData['schedule'] as $schedule) {
                foreach ($schedule as $job) {
                    if (isset($job['id'])) {
                        $workOrder = $jobs->where('id', $job['id'])->first();
                        if (!$workOrder) {
                            continue;
                        }
                        $newJob = clone $workOrder;
                        $newJob->instance_id = $job['instance_id'];
                        $newJob->start_date = Carbon::parse($job['datetime'])->format('Y-m-d');
                        $newJob->start_time = Carbon::parse($job['datetime'])->format('H:i:s');
                        $newJob->status = $this->getJobStatus($job);
                        $jobsCollection->push($newJob);
                    }
                }
            }
        }

        return $jobsCollection;
    }

    /**
     * Get job status
     *
     * @param array $job
     * @return string
     */
    private function getJobStatus($job): string
    {
        return ($job['status'] === WorkOrderStatus::COMPLETED->label()) ? WorkOrderStatus::COMPLETED->value : WorkOrderStatus::PENDING->value;
    }

    /**
     * Invalidate technician tokens
     *
     * @param Technician $technician
     * @return void
     */
    private function invalidateTechnicianTokens(Technician $technician): void
    {
        $technician->tokens()->delete();
        $technician->refresh_token = null;
        $technician->refresh_token_expires_at = null;
        $technician->save();
    }

    /**
     * Get a specific job by ID
     *
     * @param int $jobId
     * @return WorkOrder
     */
    public function getJob(int $jobId): WorkOrder
    {
        return WorkOrder::with(['business.businessChemicals.chemical', 'customer', 'checklist'])
            ->where('technician_id', auth()->id())
            ->findOrFail($jobId);
    }

    /**
     * Store customer data in completed_job_customers table
     *
     * @param WorkOrder $workOrder
     * @param array $customerData
     * @param int $instanceId
     * @return CompletedJobCustomer
     */
    protected function storeCompletedJobCustomer(WorkOrder $workOrder, array $customerData, int $instanceId): CompletedJobCustomer
    {
        // Extract image names from URLs
        $imageFields = [
            'clean_psi_image',
            'pump_image',
            'filter_image',
            'cleaner_image',
            'heat_pump_image',
            'aux_image',
            'aux2_image',
            'heater_image',
            'salt_system_image'
        ];

        $processedData = $customerData;
        foreach ($imageFields as $field) {
            if (isset($customerData[$field]) && $customerData[$field]) {
                $processedData[$field] = Str::afterLast($customerData[$field], '/');
            }
        }

        // Create completed job customer record
        return CompletedJobCustomer::create([
            'work_order_id' => $workOrder->id,
            'instance_id' => $instanceId,
            'customer_id' => $customerData['id'] ?? null,
            'name' => $customerData['name'] ?? null,
            'first_name' => $customerData['first_name'] ?? $workOrder->customer->first_name,
            'last_name' => $customerData['last_name'] ?? $workOrder->customer->last_name,
            'company_name' => $customerData['company_name'] ?? $workOrder->customer->commercial_pool_details,
            'email_1' => $customerData['email1'] ?? $workOrder->customer->email_1,
            'email_2' => $customerData['email2'] ?? $workOrder->customer->email_2,
            'phone_1' => $customerData['phone1'] ?? $workOrder->customer->phone_1,
            'phone_2' => $customerData['phone2'] ?? $workOrder->customer->phone_2,
            'address' => $customerData['address'] ?? $workOrder->customer->address,
            'street' => $customerData['street'] ?? $workOrder->customer->street,
            'city' => $customerData['city'] ?? null,
            'state' => $customerData['state'] ?? null,
            'country' => $customerData['country'] ?? null,
            'zip_code' => $customerData['zip_code'] ?? $workOrder->customer->zip_code,
            'pool_type' => $customerData['pool_type'] ?? $workOrder->customer->pool_type,
            'commercial_pool_details' => $customerData['commercial_pool_details'] ?? $workOrder->customer->commercial_pool_details,
            'pool_size_gallons' => $customerData['pool_size_gallons'] ?? $workOrder->customer->pool_size_gallons,
            'pool_length' => $customerData['pool_length'] ?? $workOrder->customer->pool_length,
            'pool_width' => $customerData['pool_width'] ?? $workOrder->customer->pool_width,
            'pool_depth' => $customerData['pool_depth'] ?? $workOrder->customer->pool_depth,
            'clean_psi' => $customerData['clean_psi'] ?? $workOrder->customer->clean_psi,
            'clean_psi_image' => $processedData['clean_psi_image'] ?? $workOrder->customer->clean_psi_image,
            'pump_details' => $customerData['pump_details'] ?? $workOrder->customer->pump_details,
            'pump_image' => $processedData['pump_image'] ?? $workOrder->customer->pump_image,
            'filter_details' => $customerData['filter_details'] ?? $workOrder->customer->filter_details,
            'filter_image' => $processedData['filter_image'] ?? $workOrder->customer->filter_image,
            'cleaner_details' => $customerData['cleaner_details'] ?? $workOrder->customer->cleaner_details,
            'cleaner_image' => $processedData['cleaner_image'] ?? $workOrder->customer->cleaner_image,
            'heat_pump_details' => $customerData['heat_pump_details'] ?? $workOrder->customer->heat_pump_details,
            'heat_pump_image' => $processedData['heat_pump_image'] ?? $workOrder->customer->heat_pump_image,
            'aux_details' => $customerData['aux_details'] ?? $workOrder->customer->aux_details,
            'aux_image' => $processedData['aux_image'] ?? $workOrder->customer->aux_image,
            'aux2_details' => $customerData['aux2_details'] ?? $workOrder->customer->aux2_details,
            'aux2_image' => $processedData['aux2_image'] ?? $workOrder->customer->aux2_image,
            'heater_details' => $customerData['heater_details'] ?? $workOrder->customer->heater_details,
            'heater_image' => $processedData['heater_image'] ?? $workOrder->customer->heater_image,
            'salt_system_details' => $customerData['salt_system_details'] ?? $workOrder->customer->salt_system_details,
            'salt_system_image' => $processedData['salt_system_image'] ?? $workOrder->customer->salt_system_image,
            'entry_instruction' => $customerData['entry_instructions'] ?? $workOrder->customer->entry_instructions,
            'tech_notes' => $customerData['tech_notes'] ?? $workOrder->customer->tech_notes,
            'admin_notes' => $customerData['admin_notes'] ?? $workOrder->customer->admin_notes,
        ]);
    }

    /**
     * Store checklist items for a completed work order
     *
     * @param WorkOrder $workOrder
     * @param array $checklist
     * @param int $instanceId
     * @return void
     */
    protected function storeWorkOrderChecklist(WorkOrder $workOrder, array $checklists, int $instanceId, bool $isRecurring = false): void
    {
        if (empty($checklists)) {
            return;
        }

        // Extract all checklist IDs
        $checklistIds = array_column($checklists, 'id');

        if ($isRecurring) {
            // For recurring jobs, create new checklist items

            foreach ($checklists as $item) {
                WorkOrderChecklistItem::create([
                    'work_order_id' => $workOrder->id,
                    'business_id' => $workOrder->business_id,
                    'description' => $item['item'] ?? null,
                    'is_completed' => true,
                    'instance_id' => $instanceId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        } else {
            // For non-recurring jobs, update existing checklist items
            WorkOrderChecklistItem::whereIn('id', $checklistIds)
                ->where('work_order_id', $workOrder->id)
                ->where('business_id', $workOrder->business_id)
                ->update([
                    'is_completed' => true,
                    'instance_id' => $instanceId,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Store chemical logs for a completed work order
     *
     * @param WorkOrder $workOrder
     * @param array $chemicals
     * @param int $instanceId
     * @return void
     */
    protected function storeChemicalLogs(WorkOrder $workOrder, array $chemicals, int $instanceId): void
    {
        $items = [];
        foreach ($chemicals as $chemical) {

            if ($chemical['qty_added'] && $chemical['qty_added'] > 0 && empty($chemical['chemical_used'])) {
                throw new ChemicalException(__('api.chemical_not_used'));
            }

            $items[] = [
                'work_order_id' => $workOrder->id,
                'instance_id' => $instanceId,
                'chemical_id' => $chemical['id'],
                'chemical_name' => $chemical['chemical'],
                'reading' => $chemical['reading'] ?? null,
                'range' => $chemical['range'] ?? null,
                'ideal_target' => $chemical['ideal_target'] ?? null,
                'unit' => $chemical['unit'] ?? null,
                'chemical_used' => $chemical['chemical_used'] ?? null,
                'qty_added' => $chemical['qty_added'] ?? null,
                'chemical_used_unit' => $chemical['chemical_used_unit'] ?? null,
                'tabs' => $chemical['tabs'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Bulk insert all chemical logs
        if (!empty($items)) {
            ChemicalLog::insert($items);
        }
    }

    /**
     * Store maintenance items for a completed work order
     *
     * @param WorkOrder $workOrder
     * @param array $maintenanceItems
     * @param int $instanceId
     * @return void
     */
    protected function storeUsedMaintenanceItems(WorkOrder $workOrder, array $maintenanceItems, int $instanceId): void
    {
        $items = [];
        foreach ($maintenanceItems as $item) {
            $items[] = [
                'work_order_id' => $workOrder->id,
                'instance_id' => $instanceId,
                'item' => $item['item'] ?? null,
                'quantity' => $item['qty'] ?? null,
                'unit' => $item['unit'] ?? null,
                'remover_added' => $item['remover_added'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Bulk insert all maintenance items
        if (!empty($items)) {
            UsedMaintenanceItem::insert($items);
        }
    }

    /**
     * Store sold items for a completed work order
     *
     * @param WorkOrder $workOrder
     * @param array $soldItems
     * @param int $instanceId
     * @return void
     */
    protected function storeItemSolds(WorkOrder $workOrder, array $soldItems, int $instanceId): void
    {
        $items = [];
        foreach ($soldItems as $soldItem) {
            $items[] = [
                'work_order_id' => $workOrder->id,
                'instance_id' => $instanceId,
                'business_id' => $workOrder->business_id,
                'item_id' => $soldItem['id'] ?? null,
                'item' => $soldItem['item'] ?? null,
                'quantity' => $soldItem['qty'] ?? null,
                'created_at' => now(),
                'updated_at' => now()
            ];
        }

        // Bulk insert all sold items
        if (!empty($items)) {
            ItemSold::insert($items);
        }
    }

    /**
     * Complete a recurring job instance and prepare the next instance
     *
     * @param WorkOrder $workOrder The work order to complete
     * @param array $completionData The data from the completion request
     * @param int $instanceId The instance ID being completed
     * @return void
     * @throws \Exception If work order is not found
     */
    protected function completeRecurringJob(WorkOrder $workOrder, array $completionData, int $instanceId, $assignment = null): void
    {
        // Build payload from request with fallbacks to work_orders fields
        $payload = [
            'status' => WorkOrderStatus::COMPLETED->value,
            'type' => $workOrder->type,
            'business_id' => $workOrder->business_id,
            'customer_id' => $completionData['customer']['id'] ?? $workOrder->customer_id,
            'template_id' => $completionData['template_id'] ?? $workOrder->template_id,
            'name' => $completionData['name'] ?? $workOrder->name,
            'description' => $completionData['description'] ?? $workOrder->description,
            'additional_task' => $completionData['additional_task'] ?? $workOrder->additional_task,
            'communication_notes' => $completionData['communication_text'] ?? null,
            'technician_customer_coordination' => (bool)($completionData['communication_notes'] ?? $workOrder->technician_customer_coordination),
            'photo' => !empty($completionData['photo']) ? basename($completionData['photo']) : null,
            'extra_work_done' => $completionData['extra_work_done'] ?? null,
            'selected_days' => json_encode($completionData['days'] ?? []),
            'completed_at' => $completionData['completed_at'] ?? now(),
        ];

        if ($assignment) {
            // Update the existing assignment
            $assignment->update($payload);
            return;
        }

        $businessTimezone = $workOrder->getBusinessTimezone();
        $startDate = $completionData['preferred_start_date'] ?? $workOrder->preferred_start_date;
        $startTime = $completionData['preferred_start_time'] ?? $workOrder->preferred_start_time;
        $jobStartTime = $startDate . ' ' . $startTime;

        $utcTime = Carbon::parse($jobStartTime, $businessTimezone)->setTimezone('UTC');

        // Insert a new assignment when none exists
        $workOrder->assignments()->create(array_merge($payload, [
            'work_order_id' => $workOrder->id,
            'instance_id' => $instanceId,
            'technician_id' => $workOrder->technician_id,
            'scheduled_date' => $utcTime->format('Y-m-d'),
            'scheduled_time' => $utcTime->format('H:i:s'),
        ]));
    }

    /**
     * Validate the recurring work order for completion
     * @param Collection $job
     * @param Collection $assignment
     * @return array
     */
    public function validateRecurringWorkOrder($job, $assignment, $jobsSyncedAt): array
    {
        $failed = false;
        $failedMessage = '';
        $errorCode = '';
        $authUserId = auth()->user()->id;

        if (($assignment && $assignment->technician_id != $authUserId) || (!$assignment && $job->technician_id != $authUserId)) {
            //Check if Job is assigned to the technician
            $failed = true;
            $failedMessage = __('api.job_not_assigned');
            $errorCode = ApiStatus::TECHNICIAN_CHANGED;
        } elseif ($jobsSyncedAt && Carbon::parse($job->updated_at)->gt(Carbon::parse($jobsSyncedAt))) {
            $failed = true;
            $failedMessage = __('api.job_details_changed');
            $errorCode = ApiStatus::JOB_DETAILS_CHANGED;
        } elseif ($assignment && $assignment->status->value == WorkOrderStatus::COMPLETED->value) {
            // Check if the job is already completed
            $failed = true;
            $failedMessage = __('api.job_already_completed');
            $errorCode = ApiStatus::JOB_ALREADY_COMPLETED;
        }

        return ['failed' => $failed, 'message' => $failedMessage, 'error_code' => $errorCode ];
    }

    /**
     * Validate the work order for completion
     * @param Collection $job
     * @return array
     */
    public function validateWorkOrder($job, $jobsSyncedAt): array
    {
        $failed = false;
        $failedMessage = '';
        $errorCode = '';
        $authUserId = auth()->user()->id;

        if ($job->status == WorkOrderStatus::COMPLETED->value) {
            $failed = true;
            $failedMessage = __('api.job_already_completed');
            $errorCode = ApiStatus::JOB_ALREADY_COMPLETED;
        } elseif ($job->technician_id != $authUserId) {
            //Check if Job is assigned to the technician
            $failed = true;
            $failedMessage = __('api.job_not_assigned');
            $errorCode = ApiStatus::TECHNICIAN_CHANGED;
        } elseif ($job->updated_at > $jobsSyncedAt) {
            //Check if Job last updated is latest than jobs synced at
            $failed = true;
            $failedMessage = __('api.job_details_changed');
            $errorCode = ApiStatus::JOB_DETAILS_CHANGED;
        }

        return ['failed' => $failed, 'message' => $failedMessage, 'error_code' => $errorCode ];
    }

    /**
     * Complete a job with provided data
     *
     * @param int $jobId
     * @param array $completionData
     * @return JsonResponse
     * @throws Throwable
     */
    public function completeJob(int $jobId, array $completionData): JsonResponse
    {
        try {
            // Start transaction
            return DB::transaction(function () use ($jobId, $completionData) {
                $instanceId = $completionData['instance_id'];
                $jobsSyncedAt = auth()->user()->jobs_synced_at;

                $job = WorkOrder::where('is_active', 1)->findOrFail($jobId);

                if ($completionData['is_recurring'] ?? $job->is_recurring) {
                    $assignment = $job->assignments()->where('instance_id', $instanceId)->first();
                    $validation = $this->validateRecurringWorkOrder($job, $assignment, $jobsSyncedAt);

                    // Complete the recurring job Instance
                    if (!$validation['failed']) {
                        $this->completeRecurringJob($job, $completionData, $instanceId, $assignment);
                    }
                } else {
                    //Check if the job is already completed
                    $validation = $this->validateWorkOrder($job, $jobsSyncedAt);

                    // Update job status and extra work
                    if (!$validation['failed']) {
                        $job->status = WorkOrderStatus::COMPLETED->value;
                        $job->completed_at = $completionData['completed_at'] ?? now();
                        $job->extra_work_done = $completionData['extra_work_done'] ?? null;
                        $job->save();
                    }
                }

                if ($validation['failed']) {
                    Log::error('Job completion validation failed', ['job_id' => $jobId,'message' => $validation['message']]);
                    return $this->errorResponse($validation['message'], error: $validation['error_code']);
                } else {
                    // Store completed job data
                    $this->storeCompletedJobData($job, $completionData, $instanceId);
                }



                $data = [
                    'work_order_id' => $job->id,
                    'instance_id' => $completionData['instance_id'],
                    'status' => 'completed',
                ];

                return $this->successResponse($data, __('api.job_completed'), Response::HTTP_OK);
            });
        } catch (Throwable $e) {
            if ($e instanceof ChemicalException) {
                return $this->errorResponse($e->getMessage(), Response::HTTP_OK);
            }
            Log::error('Job completion failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $this->errorResponse(
                __('api.job_completion_failed'),
                Response::HTTP_OK
            );
        }
    }

    /**
     * Store completed job data
     *
     * @param WorkOrder $job
     * @param array $completionData
     * @param int $instanceId
     * @return void
     */
    private function storeCompletedJobData($job, $completionData, $instanceId): void
    {
        // Store customer data
        if (!empty($completionData['customer'])) {
            $this->storeCompletedJobCustomer($job, $completionData['customer'], $instanceId);
        }

        // Store checklist items
        if (!empty($completionData['checklist']) && is_array($completionData['checklist'])) {
            $this->storeWorkOrderChecklist($job, $completionData['checklist'], $instanceId, $completionData['is_recurring']);
        }

        // Store chemical logs
        if (!empty($completionData['chemicals']) && is_array($completionData['chemicals'])) {
            $this->storeChemicalLogs($job, $completionData['chemicals'], $instanceId);
        }

        // Store maintenance items
        if (!empty($completionData['additional_maintenance_items']) && is_array($completionData['additional_maintenance_items'])) {
            $this->storeUsedMaintenanceItems($job, $completionData['additional_maintenance_items'], $instanceId);
        }

        // Store sold items
        if (!empty($completionData['item_sold']) && is_array($completionData['item_sold'])) {
            $this->storeItemSolds($job, $completionData['item_sold'], $instanceId);
        }
    }
}
