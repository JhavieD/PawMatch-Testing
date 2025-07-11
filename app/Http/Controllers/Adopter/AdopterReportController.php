<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Controller;
use App\Models\Shared\StrayReports;
use App\Models\Shared\StrayReportStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdopterReportController extends Controller
{
    public function myReports(Request $request)
    {
        $adopter = auth()->user()->adopter;
        
        $query = StrayReports::where('adopter_id', $adopter->adopter_id);

        // Your existing filtering logic...
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                ->orWhere('location', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reports = $query->orderByDesc('reported_at')->paginate(10);

        // Attach timeline for each report using the new system
        foreach ($reports as $report) {
            $timeline = StrayReportStatusLog::with('changedBy')
                ->where('adopter_id', $report->report_id) // Fix: query by report_id stored in adopter_id field
                ->orderBy('changed_at', 'asc')
                ->get()
                ->map(function($log) {
                    return [
                        'date' => $log->changed_at->format('M d, Y g:i A'),
                        'content' => $log->notes,
                        'author' => $log->changedBy ? 
                            ($log->changedBy->first_name && $log->changedBy->last_name ? 
                                $log->changedBy->first_name . ' ' . $log->changedBy->last_name : 
                                'Admin') : 'System',
                    ];
                });
            $report->timeline = $timeline;
        }

        return view('adopter.my-reports', compact('reports'));
    }

    public function show($reportId)
    {
        $adopter = auth()->user()->adopter;
        
        $report = StrayReports::where('adopter_id', $adopter->adopter_id)
            ->where('report_id', $reportId)
            ->firstOrFail();

        // Get detailed timeline using the new status logs table
        $timeline = StrayReportStatusLog::with('changedBy')
            ->where('adopter_id', $report->report_id) // Fix: query by report_id stored in adopter_id field
            ->orderBy('changed_at', 'asc')
            ->get()
            ->map(function($log) {
                return [
                    'date' => $log->changed_at->format('F d, Y g:i A'),
                    'content' => $log->notes,
                    'author' => $log->changedBy ? 
                        ($log->changedBy->first_name && $log->changedBy->last_name ? 
                            $log->changedBy->first_name . ' ' . $log->changedBy->last_name : 
                            'Admin') : 'System',
                ];
            });

        return view('adopter.report-details', compact('report', 'timeline'));
    }
}