<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $queryCount = 0;

        // Monitor database queries
        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
            
            // Log slow queries (over 100ms)
            if ($query->time > 100) {
                Log::warning('Slow query detected', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms',
                    'url' => request()->url()
                ]);
            }
        });

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Log slow page loads (over 500ms)
        if ($executionTime > 500) {
            Log::warning('Slow page load detected', [
                'url' => $request->url(),
                'execution_time' => round($executionTime, 2) . 'ms',
                'query_count' => $queryCount
            ]);
        }

        return $response;
    }
} 