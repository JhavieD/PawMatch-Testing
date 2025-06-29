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

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $activeAdoptions = AdoptionApplication::where('status', 'approved')->count();
        $pendingReports = 2; // This will be replaced with actual stray reports count
        $newUsersToday = User::whereDate('created_at', today())->count();

        return view('admin.admin_dashboard', compact('totalUsers', 'activeAdoptions', 'pendingReports', 'newUsersToday'));
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
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply role filter
        if ($request->has('role') && $request->role !== '') {
            $query->where('role', $request->role);
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status === 'active');
        }

        $users = $query->latest()->get();

        $stats = [
            'total' => User::count(),
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
            return response()->json([
                'message' => 'Cannot delete admin users'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
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


    public function settings()
    {
        return view('admin.settings');
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email',
            'maintenance_mode' => 'boolean',
            'notifications_enabled' => 'boolean',
        ]);

        // Store settings (this will be replaced with actual settings storage)
        return response()->json([
            'message' => 'Settings updated successfully'
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
            $query = \App\Models\StrayReports::with('adopter');

            // Search filter (searches description and location)
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->where('description', 'like', "%$search%")
                    ->orWhere('location', 'like', "%$search%");
                });
            }

            // Status filter
            if ($request->filled('status')) {
                $query->where('status', $request->input('status'));
            }

            // Paginate reports
            $reports = $query->orderByDesc('reported_at')->paginate(20);

            // Attach timeline to each report
            foreach ($reports as $report) {
                $timeline = \DB::table('admin_actions')
                    ->where('action_type', 'status_update')
                    ->where('target_report_id', $report->report_id)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function($action) {
                        return [
                            'date' => \Carbon\Carbon::parse($action->created_at)->format('F d, Y h:i A'),
                            'content' => $action->reason,
                        ];
                    });
                $report->timeline = $timeline;
            }

            return view('admin.stray-reports', compact('reports'));
        }

        public function updateStatus(Request $request, $id)
    {
        try {
            $report = \App\Models\StrayReports::find($id);
            if (!$report) {
                return response()->json(['success' => false, 'message' => 'Report not found.'], 404);
            }

            $request->validate([
                'status' => 'required|in:pending,investigating,resolved,cancelled'
            ]);

            $report->status = $request->status;
            $report->save();

            DB::table('admin_actions')->insert([
                'action_type' => 'status_update',
                'target_report_id' => $report->id,
                'reason' => 'Status updated to ' . $request->status,
                'created_at' => now(),
                'updated_at' => now(),
                'admin_id' => auth()->id(),
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
        
    public function strayReportTimeline($id)
            {
                $timeline = \DB::table('admin_actions')
                    ->where('action_type', 'status_update')
                    ->where('target_report_id', $id)
                    ->orderBy('created_at', 'asc')
                    ->get()
                    ->map(function($action) {
                        return [
                            'date' => \Carbon\Carbon::parse($action->created_at)->format('F d, Y h:i A'),
                            'content' => $action->reason,
                        ];
                    });
                return response()->json(['timeline' => $timeline]);
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
                'rescuer_verifications.document_url',
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
        $verifications = $shelterVerifications->union($rescuerVerifications)
            ->orderBy('submitted_at', 'desc')
            ->get();

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

    public function showVerification($id)
    {
        // First try to find in shelter verifications
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

        if (!$verification) {
            // If not found, try rescuer verifications
            $verification = DB::table('rescuer_verifications')
                ->join('users', 'rescuer_verifications.submitted_by', '=', 'users.user_id')
                ->join('rescuers', 'rescuer_verifications.rescuer_id', '=', 'rescuers.rescuer_id')
                ->select(
                    'rescuer_verifications.verification_id',
                    'rescuer_verifications.submitted_by',
                    'rescuer_verifications.document_url',
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
        }

        if (!$verification) {
            return response()->json(['error' => 'Verification not found'], 404);
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
        // Try to update in shelter verifications
        $updated = DB::table('shelter_verifications')
            ->where('verification_id', $id)
            ->update([
                'status' => $status,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now()
            ]);

        if (!$updated) {
            // If not found, try rescuer verifications
            $updated = DB::table('rescuer_verifications')
                ->where('verification_id', $id)
                ->update([
                    'status' => $status,
                    'reviewed_by' => auth()->id(),
                    'reviewed_at' => now()
                ]);
        }

        if (!$updated) {
            return response()->json(['error' => 'Verification not found'], 404);
        }

        // Send notification to user
        $verification = DB::table('shelter_verifications')
            ->where('verification_id', $id)
            ->first() ?? DB::table('rescuer_verifications')
            ->where('verification_id', $id)
            ->first();

        $user = User::find($verification->submitted_by);
        
        // You can implement notification logic here
        // Notification::send($user, new VerificationStatusUpdated($status));

        return response()->json(['message' => 'Verification status updated successfully']);
    }
} 