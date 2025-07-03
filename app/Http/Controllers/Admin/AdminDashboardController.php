<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shared\User;
use App\Models\Shared\Pet;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shelter;
use App\Models\Adopter;
use App\Models\Rescuer;
use App\Models\Shared\StrayReports;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules;
use App\Http\Controllers\Shared\Controller;
use Illuminate\Support\Facades\Artisan;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeAdoptions = AdoptionApplication::where('status', 'approved')->count();
        $pendingReports = StrayReports::where('status', 'pending')->count();
        $investigatingReports = StrayReports::where('status', 'investigating')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();

        // Recent activity
        $recentReports = StrayReports::with('adopter.user')
            ->orderByDesc('reported_at')
            ->limit(5)
            ->get();

        return view('admin.admin_dashboard', compact(
            'totalUsers', 
            'activeAdoptions', 
            'pendingReports', 
            'investigatingReports',
            'newUsersToday',
            'recentReports'
        ));
    }

    public function applications(Request $request)
    {
        $query = AdoptionApplication::with(['adopter.user', 'pet', 'shelter']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Search by adopter name or pet name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('adopter.user', function($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%");
                })
                ->orWhereHas('pet', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        $applications = $query->orderBy('submitted_at', 'desc')->paginate(15);

        return view('admin.applications', compact('applications'));
    }

    public function users(Request $request)
    {
        $query = User::query();

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(15)->appends($request->query());

        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'banned' => User::where('status', 'banned')->count(),
            'shelters' => User::where('role', 'shelter')->count(),
            'adopters' => User::where('role', 'adopter')->count(),
            'rescuers' => User::where('role', 'rescuer')->count(),
        ];

        return view('admin.users', compact('users', 'stats'));
    }

    public function pets(Request $request)
    {
        $query = Pet::with(['shelter']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('adoption_status', $request->status);
        }

        // Search by pet name or shelter name
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('shelter', function($q) use ($search) {
                      $q->where('shelter_name', 'like', "%{$search}%");
                  });
            });
        }

        $pets = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.pets', compact('pets'));
    }

    public function deleteUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot delete admin user'], 403);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' => 'User deleted']);
    }

    public function toggleUserStatus(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json([
                'message' => 'Cannot modify admin user status'
            ], 403);
        }

        $user->update([
            'status' => !$user->status
        ]);

        return response()->json([
            'message' => 'User status updated successfully',
            'status' => $user->status
        ]);
    }

    public function statistics()
    {
        // Detailed statistics for admin
        $stats = [
            // User statistics
            'user_stats' => [
                'total' => User::count(),
                'admins' => User::where('role', 'admin')->count(),
                'shelters' => User::where('role', 'shelter')->count(),
                'adopters' => User::where('role', 'adopter')->count(),
                'rescuers' => User::where('role', 'rescuer')->count(),
                'new_this_month' => User::whereMonth('created_at', Carbon::now()->month)->count(),
            ],
            
            // Pet statistics
            'pet_stats' => [
                'total' => Pet::count(),
                'available' => Pet::where('adoption_status', 'available')->count(),
                'pending' => Pet::where('adoption_status', 'pending')->count(),
                'adopted' => Pet::where('adoption_status', 'adopted')->count(),
                'by_species' => Pet::select('species', DB::raw('count(*) as total'))
                    ->groupBy('species')
                    ->get(),
            ],
            
            // Application statistics
            'application_stats' => [
                'total' => AdoptionApplication::count(),
                'pending' => AdoptionApplication::where('status', 'pending')->count(),
                'approved' => AdoptionApplication::where('status', 'approved')->count(),
                'rejected' => AdoptionApplication::where('status', 'rejected')->count(),
                'success_rate' => $this->calculateSuccessRate(),
            ],
        ];

        // Monthly trends for the last 12 months
        $monthlyTrends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyTrends[] = [
                'month' => $date->format('M Y'),
                'new_users' => User::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'new_pets' => Pet::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'applications' => AdoptionApplication::whereYear('submitted_at', $date->year)
                    ->whereMonth('submitted_at', $date->month)
                    ->count(),
                'adoptions' => AdoptionApplication::where('status', 'approved')
                    ->whereYear('submitted_at', $date->year)
                    ->whereMonth('submitted_at', $date->month)
                    ->count(),
            ];
        }

        return view('admin.statistics', compact('stats', 'monthlyTrends'));
    }

    private function calculateSuccessRate()
    {
        $totalApplications = AdoptionApplication::count();
        $approvedApplications = AdoptionApplication::where('status', 'approved')->count();
        
        return $totalApplications > 0 ? round(($approvedApplications / $totalApplications) * 100, 2) : 0;
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => ['required', 'string', 'in:activate,deactivate,delete'],
            'user_ids' => ['required', 'array'],
            'user_ids.*' => ['exists:users,id']
        ]);

        $users = User::whereIn('id', $request->user_ids)
                    ->where('role', '!=', 'admin')
                    ->get();

        switch ($request->action) {
            case 'activate':
                $users->each->update(['status' => true]);
                $message = 'Users activated successfully';
                break;
            case 'deactivate':
                $users->each->update(['status' => false]);
                $message = 'Users deactivated successfully';
                break;
            case 'delete':
                $users->each->delete();
                $message = 'Users deleted successfully';
                break;
        }

        return response()->json([
            'message' => $message
        ]);
    }

    public function getUserActivity($id)
    {
        $user = User::findOrFail($id);
        
        // Get user's activity log (you'll need to implement activity logging)
        $activities = [
            [
                'description' => 'Logged in to the system',
                'created_at' => now()->subHours(2),
            ],
            [
                'description' => 'Updated profile information',
                'created_at' => now()->subDays(1),
            ],
            [
                'description' => 'Changed password',
                'created_at' => now()->subDays(3),
            ],
            [
                'description' => 'Account created',
                'created_at' => $user->created_at,
            ],
        ];

        return response()->json($activities);
    }

    public function exportUsers()
    {
        $users = User::all();
        $csvFileName = 'users_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$csvFileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID', 'Name', 'Email', 'Role', 'Status', 'Joined Date'];

        $callback = function() use ($users, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->role,
                    $user->status ? 'Active' : 'Inactive',
                    $user->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,shelter,adopter,rescuer'],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => true,
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,shelter,adopter,rescuer'],
            'phone' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'boolean'],
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'phone' => $request->phone,
            'status' => $request->status,
        ]);

        if ($request->has('password') && $request->password) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);

            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function profile()
    {
        return view('admin.profile', [
            'user' => auth()->user()
        ]);    
    }

    /**
     * Show the settings page with maintenance mode status.
     */
    public function settings()
    {
        $isMaintenance = app()->isDownForMaintenance();
        // ...fetch other settings as needed...
        return view('admin.settings', [
            'isMaintenance' => $isMaintenance,
            // ...other settings...
        ]);
    }

    /**
     * Toggle Laravel's built-in maintenance mode.
     */
    public function toggleMaintenance(Request $request)
    {
        if ($request->has('maintenance_mode')) {
            // List of admin IPs to allow during maintenance (edit as needed)
            $adminIps = [
                '127.0.0.1', // Localhost IPv4
                '::1',       // Localhost IPv6
                // Add your real public IP(s) below for production, e.g.:
                // '203.0.113.42',
            ];
            foreach ($adminIps as $ip) {
                Artisan::call('down', [
                    '--allow' => $ip,
                ]);
            }
        } else {
            Artisan::call('up');
        }
        return redirect()->back()->with('status', 'Maintenance mode updated!');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'notifications_enabled' => 'boolean',
        ]);

        // Store settings (this will be replaced with actual settings storage)
        $isMaintenance = app()->isDownForMaintenance();
        return view('admin.settings', [
            'isMaintenance' => $isMaintenance,
            // ...other settings...
        ]);
    }

    public function assignReport(Request $request, $reportId)
    {
        // This will be replaced with actual report assignment functionality
        return response()->json([
            'message' => 'Report assigned successfully'
        ]);
    }

    public function strayReports(Request $request)
    {
        // Start query with adopter relationship
        $query = StrayReports::with('adopter.user');

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

        $reports = $query->orderByDesc('reported_at')->paginate(12);

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

        return view('admin.stray-reports', compact('reports'));
    }

    public function updateStatus(Request $request, $id)
    {
        try {
            $report = StrayReports::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }

            $request->validate([
                'status' => 'required|in:pending,investigating,resolved,cancelled'
            ]);

            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            $oldStatus = $report->status;
            $report->status = $request->status;
            $report->save();

            // Insert admin action
            DB::table('admin_actions')->insert([
                'action_type' => 'status_update',
                'target_report_id' => $report->report_id,
                'reason' => "Status updated from {$oldStatus} to {$request->status}",
                'admin_id' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json(['success' => true, 'message' => 'Status updated successfully']);
        } catch (\Exception $e) {
            \Log::error('Update status error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
        
    public function strayReportTimeline($id)
    {
        $timeline = \DB::table('admin_actions')
            ->leftJoin('users', 'admin_actions.admin_id', '=', 'users.user_id')
            ->where('target_report_id', $id)
            ->where('action_type', 'status_update') // Only status updates in timeline
            ->orderBy('created_at', 'desc') // Latest first
            ->select('admin_actions.*', 'users.first_name', 'users.last_name')
            ->get()
            ->map(function($action) {
                return [
                    'date' => \Carbon\Carbon::parse($action->created_at)->format('F d, Y h:i A'),
                    'content' => $action->reason,
                    'type' => $action->action_type,
                    'author' => ($action->first_name && $action->last_name) 
                        ? $action->first_name . ' ' . $action->last_name 
                        : 'Admin',
                ];
            });
        return response()->json(['timeline' => $timeline]);
    }
    
    //for comments ito
    public function strayReportComments($id)
    {
        $comments = \DB::table('admin_actions') //table nito for comments
            ->leftJoin('users', 'admin_actions.admin_id', '=', 'users.user_id')
            ->where('action_type', 'comment')
            ->where('target_report_id', $id)
            ->orderBy('created_at', 'desc') // descending order ng comments una ung latest
            ->select('admin_actions.*', 'users.first_name', 'users.last_name')
            ->get()
            ->map(function($action) {
                return [
                    'author' => ($action->first_name && $action->last_name) 
                        ? $action->first_name . ' ' . $action->last_name 
                        : 'Admin',
                    'date' => \Carbon\Carbon::parse($action->created_at)->format('F d, Y h:i A'),
                    'content' => $action->reason,
                ];
            });
        return response()->json(['comments' => $comments]);
    }
  
    public function verifications()
    {
        // Get all verifications from different tables
        $shelterVerifications = DB::table('shelter_verifications')
            ->join('users', 'shelter_verifications.submitted_by', '=', 'users.user_id')
            ->join('shelters', 'shelter_verifications.shelter_id', '=', 'shelters.shelter_id')
            ->select(
                'shelter_verifications.verification_id',
                'shelter_verifications.submitted_by',
                'shelter_verifications.registration_doc_url as document_url',
                'shelter_verifications.facebook_link',
                'shelter_verifications.status',
                'shelter_verifications.submitted_at',
                'shelter_verifications.reviewed_at',
                'shelter_verifications.reviewed_by',
                'users.first_name',
                'users.last_name',
                'users.email',
                'shelters.shelter_name as organization_name',
                DB::raw("'shelter' as type")
            );

        $rescuerVerifications = DB::table('rescuer_verifications')
            ->join('users', 'rescuer_verifications.submitted_by', '=', 'users.user_id')
            ->join('rescuers', 'rescuer_verifications.rescuer_id', '=', 'rescuers.rescuer_id')
            ->select(
                'rescuer_verifications.verification_id',
                'rescuer_verifications.submitted_by',
                'rescuer_verifications.document_url as rescuer_document_url',
                'rescuer_verifications.facebook_link',
                'rescuer_verifications.status',
                'rescuer_verifications.submitted_at',
                'rescuer_verifications.reviewed_at',
                'rescuer_verifications.reviewed_by',
                'users.first_name',
                'users.last_name',
                'users.email',
                'rescuers.organization_name',
                DB::raw("'rescuer' as type")
            );

        // Combine and sort by submission date
        $verifications = DB::table(DB::raw("({$shelterVerifications->toSql()} UNION {$rescuerVerifications->toSql()}) as combined"))
            ->mergeBindings($shelterVerifications->union($rescuerVerifications))
            ->orderBy('submitted_at', 'desc')
            ->get();
        // Work in Progres

        // Get counts for stats
        $stats = [
            'pending' => $verifications->where('status', 'pending')->count(),
            'approved_today' => $verifications->where('status', 'approved')
                ->where('reviewed_at', '>=', now()->startOfDay())
                ->count(),
            'rejected_today' => $verifications->where('status', 'rejected')
                ->where('reviewed_at', '>=', now()->startOfDay())
                ->count(),
        ];

        return view('admin.verifications', compact('verifications', 'stats'));
    }
    // Shelter and Rescuer Verification
    public function showVerification($id)
    {
        $type = request()->query('type');

        if ($type === 'shelter') {
            $verification = DB::table('shelter_verifications')
                ->join('users', 'shelter_verifications.submitted_by', '=', 'users.user_id')
                ->join('shelters', 'shelter_verifications.shelter_id', '=', 'shelters.shelter_id')
                ->select(
                    'shelter_verifications.verification_id',
                    'shelter_verifications.submitted_by',
                    'shelter_verifications.registration_doc_url as document_url',
                    'shelter_verifications.facebook_link',
                    'shelter_verifications.status',
                    'shelter_verifications.submitted_at',
                    'shelter_verifications.reviewed_at',
                    'shelter_verifications.reviewed_by',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'shelters.shelter_name as organization_name',
                    DB::raw("'shelter' as type")
                )
                ->where('shelter_verifications.verification_id', $id)
                ->first();
        } elseif ($type === 'rescuer') {
            $verification = DB::table('rescuer_verifications')
                ->join('users', 'rescuer_verifications.submitted_by', '=', 'users.user_id')
                ->join('rescuers', 'rescuer_verifications.rescuer_id', '=', 'rescuers.rescuer_id')
                ->select(
                    'rescuer_verifications.verification_id',
                    'rescuer_verifications.submitted_by',
                    'rescuer_verifications.document_url as document_url',
                    'rescuer_verifications.facebook_link',
                    'rescuer_verifications.status',
                    'rescuer_verifications.submitted_at',
                    'rescuer_verifications.reviewed_at',
                    'rescuer_verifications.reviewed_by',
                    'users.first_name',
                    'users.last_name',
                    'users.email',
                    'rescuers.organization_name',
                    DB::raw("'rescuer' as type")
                )
                ->where('rescuer_verifications.verification_id', $id)
                ->first();
        } else {
            return response()->json(['error' => 'Invalid or missing verification type'], 400);
        }

        if (!$verification) {
            return response()->json(['error' => 'Verification not found'], 404);
        }

        if ($verification->document_url) {
            $verification->document_url = Storage::disk('s3')->url($verification->document_url);
        }

        return response()->json($verification);
    }

    public function approveVerification($id)
    {
        return $this->updateVerificationStatus($id, 'approved');
    }

    public function rejectVerification($id)
    {
        return $this->updateVerificationStatus($id, 'rejected');
    }

    private function updateVerificationStatus($id, $status)
    {
        $type = request()->query('type');

        if ($type === 'shelter') {
            $updated = DB::table('shelter_verifications')
                ->where('verification_id', $id)
                ->update([
                    'status' => $status,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'updated_at' => now()
                ]);
        } elseif ($type === 'rescuer') {
            $updated = DB::table('rescuer_verifications')
                ->where('verification_id', $id)
                ->update([
                    'status' => $status,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now(),
                    'updated_at' => now()
                ]);
        } else {
            return response()->json(['error' => 'Invalid verification type'], 400);
        }

        if (!$updated) {
            return response()->json(['error' => 'Verification not found'], 404);
        }

        $verification = DB::table('shelter_verifications')
            ->where('verification_id', $id)
            ->first() ?? DB::table('rescuer_verifications')
            ->where('verification_id', $id)
            ->first();

        $user = User::find($verification->submitted_by);
        
        return redirect()->back()->with('success', 'Verification status updated.');
    }

    public function addComment(Request $request, $id)
    {
        try {
            $report = StrayReports::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }

            $request->validate([
                'comment' => 'required|string|max:1000'
            ]);

            // Check if user is authenticated
            if (!auth()->check()) {
                return response()->json(['success' => false, 'message' => 'User not authenticated.'], 401);
            }

            DB::table('admin_actions')->insert([
                'action_type' => 'comment',
                'target_report_id' => $report->report_id,
                'reason' => $request->comment,
                'admin_id' => auth()->id(),
                'created_at' => now(),
                // Remove updated_at if the column doesn't exist in your migration
            ]);

            // Get user info safely
            $user = auth()->user();
            $authorName = 'Admin'; // default
            if ($user && $user->first_name && $user->last_name) {
                $authorName = $user->first_name . ' ' . $user->last_name;
            } elseif ($user && $user->name) {
                $authorName = $user->name;
            }

            return response()->json([
                'success' => true,
                'comment' => [
                    'author' => $authorName,
                    'date' => now()->format('F d, Y h:i A'),
                    'content' => $request->comment
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Add comment error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function findNearbyShelters($reportId)
    {
        try {
            $report = StrayReports::findOrFail($reportId);
            $reportLocation = strtolower($report->location);
            $locationKeywords = [];
            
            $parts = preg_split('/[,|]/', $reportLocation);
            foreach ($parts as $part) {
                $cleaned = trim($part);
                
                if (preg_match('/\b(\w+(?:\s+\w+)?)\s+(city|town|municipality)\b/i', $cleaned, $matches)) {
                    $cityName = strtolower(trim($matches[1]));
                    if (strlen($cityName) > 2) { 
                        $locationKeywords[] = $cityName;
                    }
                }
                
                $words = preg_split('/\s+/', $cleaned);
                foreach ($words as $word) {
                    $word = strtolower(trim($word));
                    
                    if (strlen($word) > 4 && 
                        !in_array($word, ['street', 'barangay', 'subdivision', 'village', 'city', 'town', 'municipality', 'province'])) {
                        $locationKeywords[] = $word;
                    }
                }
            }
            
            $locationKeywords = array_unique(array_filter($locationKeywords));
            
            // Find shelters - INCLUDE ALL SHELTERS (verified and unverified)
            $shelters = \DB::table('shelters')
                ->join('users', 'shelters.user_id', '=', 'users.user_id')
                ->select(
                    'shelters.shelter_id',
                    'shelters.shelter_name',
                    'shelters.location',
                    'shelters.contact_info',
                    'shelters.verified', 
                    'users.email',
                    'users.first_name',
                    'users.last_name'
                )
                ->get();

            // Sort shelters by location relevance with verification status
            $sortedShelters = $shelters->map(function($shelter) use ($locationKeywords) {
                $shelterLocation = strtolower($shelter->location);
                $matchScore = 0;
                
                foreach ($locationKeywords as $keyword) {
                    if (strpos($shelterLocation, $keyword) !== false) {
                        $matchScore += 1;
                    }
                    
                    // Enhanced matching for cities
                    if (preg_match('/\b(\w+(?:\s+\w+)?)\s+(city|town|municipality)\b/i', $shelterLocation, $shelterMatches)) {
                        $shelterCityName = strtolower(trim($shelterMatches[1]));
                        if ($keyword === $shelterCityName) {
                            $matchScore += 3; 
                        }
                    }
                }
                
                $shelter->match_score = $matchScore;
                $shelter->distance_text = $matchScore >= 2 ? 'Same Area' : 'Different Area';
                
                // ADD VERIFICATION STATUS TO SHELTER NAME
                $shelter->display_name = $shelter->shelter_name . ($shelter->verified ? '' : ' (Unverified)');
                $shelter->verification_status = $shelter->verified ? 'verified' : 'unverified';
                
                return $shelter;
            })->sortByDesc(function($shelter) {
                // PRIORITIZE VERIFIED SHELTERS, THEN BY MATCH SCORE
                return ($shelter->verified ? 1000 : 0) + $shelter->match_score;
            });

            return response()->json([
                'success' => true,
                'shelters' => $sortedShelters->values()->all()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    public function markAsInvestigating(Request $request, $reportId)
    {
        try {
            $request->validate([
                'selected_shelters' => 'required|array',
                'selected_shelters.*' => 'exists:shelters,shelter_id',
                'notification_message' => 'nullable|string|max:500'
            ]);

            $report = StrayReports::findOrFail($reportId);
            
            // Check if already investigating
            if ($report->status === 'investigating') {
                return response()->json([
                    'success' => false,
                    'message' => 'Report is already being investigated'
                ], 400);
            }

            // Update report status
            $report->status = 'investigating';
            $report->save();

            // Get shelter names for the timeline message
            $shelterNames = \DB::table('shelters')
                ->whereIn('shelter_id', $request->selected_shelters)
                ->pluck('shelter_name')
                ->toArray();

            $shelterList = implode(', ', $shelterNames);

            // CREATE NOTIFICATION RECORDS FOR EACH SELECTED SHELTER
            foreach ($request->selected_shelters as $shelterId) {
                \DB::table('stray_report_notifications')->insert([
                    'report_id' => $report->report_id,
                    'shelter_id' => $shelterId,
                    'sent_at' => now(),
                    'is_read' => false,
                    'admin_message' => $request->notification_message, 
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Create admin action for timeline
            \DB::table('admin_actions')->insert([
                'action_type' => 'status_update',
                'target_report_id' => $report->report_id,
                'reason' => "Status changed to investigating. Notified shelters: {$shelterList}",
                'admin_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report marked as investigating and shelters notified successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function activateUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot modify admin user'], 403);
        }
        $user->status = 'active';
        $user->save();
        return response()->json(['success' => true, 'message' => 'User activated']);
    }

    public function deactivateUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot modify admin user'], 403);
        }
        $user->status = 'inactive';
        $user->save();
        return response()->json(['success' => true, 'message' => 'User deactivated']);
    }

    public function banUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot ban admin user'], 403);
        }
        $user->status = 'banned';
        $user->save();
        return response()->json(['success' => true, 'message' => 'User banned']);
    }

    public function unbanUser(User $user)
    {
        if ($user->role === 'admin') {
            return response()->json(['success' => false, 'message' => 'Cannot unban admin user'], 403);
        }
        $user->status = 'active';
        $user->save();
        return response()->json(['success' => true, 'message' => 'User unbanned']);
    }

    /**
     * Show user details for AJAX requests.
     */
    public function showUser(User $user)
    {
        // You can load relationships as needed, e.g. $user->load('adopter', 'shelter', 'rescuer');
        return response()->json($user);
    }
}