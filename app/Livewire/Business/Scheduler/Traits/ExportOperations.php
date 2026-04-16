<?php

declare(strict_types=1);

namespace App\Livewire\Business\Scheduler\Traits;

use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait ExportOperations
{
    /**
     * Export jobs for a specific technician and date
     *
     * @param int $technicianId
     * @param string $date
     * @return StreamedResponse|bool
     */
    public function exportDayJobs(int $technicianId, string $date): StreamedResponse|bool
    {
        try {
            $response = ['success' => true, 'message' => 'business.scheduler.export_success'];
            $technician = $this->exportService->getTechnicianForExport($technicianId);

            $businessTimezone = $this->dateTimeService->getBusinessTimezone();
            $jobs = $this->exportService->getJobsForExport($technician, $date, $businessTimezone);

            if (!empty($jobs)) {
                $filename = $this->exportService->generateExportFilename($technician, $date);
                $headers = $this->exportService->getExportHeaders($filename);
            } else {
                $response = ['success' => false, 'message' => 'business.scheduler.export_error'];
            }

            $this->notify($response['success'] ? 'success' : 'error', $response['message']);

            return response()->streamDownload(
                $this->exportService->createCsvCallback($jobs),
                $filename,
                $headers
            );
        } catch (ModelNotFoundException $e) {
            $this->notify('error', 'business.scheduler.unauthorized');
            return false;
        } catch (\Throwable $e) {
            $this->notify('error', 'business.scheduler.export_error');
            return false;
        }
    }
}
