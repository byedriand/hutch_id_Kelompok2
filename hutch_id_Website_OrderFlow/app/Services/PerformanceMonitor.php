<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    /**
     * Enable query logging for performance monitoring
     * 
     * Usage: Add to .env: MONITOR_QUERIES=true
     * Then check storage/logs/queries.log
     */
    public static function enableQueryLogging()
    {
        if (!env('MONITOR_QUERIES', false)) {
            return;
        }

        DB::listen(function ($query) {
            // Log slow queries (over 1000ms)
            if ($query->time > 1000) {
                Log::channel('queries')->warning('SLOW QUERY: ' . $query->time . 'ms', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                ]);
            }

            // Log all queries in debug mode
            if (env('APP_DEBUG')) {
                Log::channel('queries')->debug('Query (' . $query->time . 'ms): ' . $query->sql, [
                    'bindings' => $query->bindings,
                ]);
            }
        });
    }

    /**
     * Get database connection stats
     */
    public static function getStats()
    {
        return [
            'query_count' => count(DB::getQueryLog()),
            'total_time' => collect(DB::getQueryLog())->sum('time') . 'ms',
            'queries' => DB::getQueryLog(),
        ];
    }

    /**
     * Reset query log
     */
    public static function reset()
    {
        DB::flushQueryLog();
    }
}
