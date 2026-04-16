<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait PerformanceMonitoring
{
    private $queryStartTime;
    private $memoryStartUsage;
    private $queryCount = 0;
    private $queryLog = [];
    private $totalQueryTime = 0;
    private $slowQueries = [];

    protected function startPerformanceMonitoring(): void
    {
        $this->queryStartTime = microtime(true);
        $this->memoryStartUsage = memory_get_usage(true);
        $this->queryLog = [];

        DB::flushQueryLog();
        DB::enableQueryLog();

        // Capture callsite when query executes
        DB::listen(function ($query) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 50);

            $caller = collect($trace)
                ->first(function ($frame) {
                    return isset($frame['file'])
                        && str_starts_with($frame['file'], base_path('app')) // Must be inside /app
                        && !str_contains($frame['file'], 'PerformanceMonitoring.php') // Avoid trait
                        && !str_contains($frame['file'], 'vendor'); // Skip vendor completely
                });

            // Format bindings
            $sql = $query->sql;
            foreach ($query->bindings as $binding) {
                $sql = preg_replace('/\?/', is_numeric($binding) ? $binding : "'{$binding}'", $sql, 1);
            }

            $this->totalQueryTime += $query->time;

            $this->queryLog[] = [
                'sql'   => $sql,
                'time'  => $query->time . 'ms',
                'file'  => $caller['file'] ?? null,
                'line'  => $caller['line'] ?? null,
            ];

            // Queries taking more than 100ms
            if ($query->time > 100) {
                $this->slowQueries[] = [
                    'sql'   => $sql,
                    'time'  => $query->time . 'ms',
                    'file'  => $caller['file'] ?? null,
                    'line'  => $caller['line'] ?? null,
                ];
            }
        });
    }

    protected function endPerformanceMonitoring(string $operation): void
    {
        $executionTime = microtime(true) - $this->queryStartTime;
        $currentMemory = memory_get_usage(true);
        $memoryUsage = max(0, $currentMemory - $this->memoryStartUsage);

        Log::channel('performance')->info("Performance Metrics", [
            'operation' => $operation,
            'execution_time' => round(microtime(true) - $this->queryStartTime, 4) . 's',
            'memory_usage' => $this->formatBytes(memory_get_usage(true) - $this->memoryStartUsage),
            'query_count' => count($this->queryLog),
            'total_query_time' => round($this->totalQueryTime, 4) . ' ms',
            'peak_memory' => $this->formatBytes(memory_get_peak_usage(true)),
            'queries' => $this->queryLog, // Use our captured queries
        ]);

        // Store slow queries for analysis
        if (!empty($this->slowQueries)) {
            Log::channel('performance')->warning('Slow Queries Detected', [
                'operation' => $operation,
                'queries' => $this->slowQueries
            ]);
        }

        // Cache performance metrics for monitoring
        $this->cachePerformanceMetrics($operation, $executionTime, $memoryUsage, count($this->queryLog));

        // Disable and flush query logging to isolate subsequent operations (connection-level)
        DB::disableQueryLog();
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    private function cachePerformanceMetrics(string $operation, float $executionTime, int $memoryUsage, int $queryCount): void
    {
        $key = "performance_metrics:{$operation}:" . date('Y-m-d');
        $metrics = Cache::get($key, [
            'count' => 0,
            'total_time' => 0,
            'total_memory' => 0,
            'total_queries' => 0,
            'max_time' => 0,
            'max_memory' => 0
        ]);

        $metrics['count']++;
        $metrics['total_time'] += $executionTime;
        $metrics['total_memory'] += $memoryUsage;
        $metrics['total_queries'] += $queryCount;
        $metrics['max_time'] = max($metrics['max_time'], $executionTime);
        $metrics['max_memory'] = max($metrics['max_memory'], $memoryUsage);
        $metrics['avg_time'] = $metrics['total_time'] / $metrics['count'];
        $metrics['avg_memory'] = $metrics['total_memory'] / $metrics['count'];
        $metrics['avg_queries'] = $metrics['total_queries'] / $metrics['count'];

        Cache::put($key, $metrics, now()->addDays(7));
    }
}
