<?php

namespace App\Http\Controllers\Adopter;

use App\Http\Controllers\Shared\Controller;
use App\Models\Shared\StrayReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdopterReportController extends Controller
{
    public function myReports(Request $request)
    {
        $adopter = auth()->user()->adopter;
        
        if (!$adopter) {
            return redirect()->route('adopter.dashboard')->with('error', 'Adopter profile not found.');
        }

        // Get reports submitted by this adopter
        $query = StrayReports::where('adopter_id', $adopter->adopter_id);

        // Filter by search term
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('animal_type', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $reports = $query->orderByDesc('reported_at')->paginate(10);

        // Attach timeline for each report
        foreach ($reports as $report) {
            $timeline = DB::table('admin_actions')
                ->leftJoin('users', 'admin_actions.admin_id', '=', 'users.user_id')
                ->where('target_report_id', $report->report_id)
                ->where('action_type', 'status_update')
                ->orderBy('created_at', 'asc')
                ->select('admin_actions.*', 'users.first_name', 'users.last_name')
                ->get()
                ->map(function($action) {
                    return [
                        'date' => \Carbon\Carbon::parse($action->created_at)->format('M d, Y g:i A'),
                        'content' => $action->reason,
                        'author' => ($action->first_name && $action->last_name) 
                            ? $action->first_name . ' ' . $action->last_name 
                            : 'Admin',
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

        // Get detailed timeline
        $timeline = DB::table('admin_actions')
            ->leftJoin('users', 'admin_actions.admin_id', '=', 'users.user_id')
            ->where('target_report_id', $report->report_id)
            ->where('action_type', 'status_update')
            ->orderBy('created_at', 'asc')
            ->select('admin_actions.*', 'users.first_name', 'users.last_name')
            ->get()
            ->map(function($action) {
                return [
                    'date' => \Carbon\Carbon::parse($action->created_at)->format('F d, Y g:i A'),
                    'content' => $action->reason,
                    'author' => ($action->first_name && $action->last_name) 
                        ? $action->first_name . ' ' . $action->last_name 
                        : 'Admin',
                ];
            });

        return view('adopter.report-details', compact('report', 'timeline'));
    }
}