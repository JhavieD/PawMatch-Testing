<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shared\User;
use App\Models\Shared\Pet;
use App\Models\Shared\AdoptionApplication;
use App\Models\Shelter\Shelter;
use App\Models\Adopter\Adopter;
use App\Models\Rescuer\Rescuer;
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
use App\Models\Shared\StrayReportStatusLog;
use App\Models\Shared\MayaTransaction;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Response;


class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::count();
        $adoptionPipeline = [
            'pending'  => AdoptionApplication::where('status', 'pending')->count(),
            'approved' => AdoptionApplication::where('status', 'approved')->count(),
            'rejected' => AdoptionApplication::where('status', 'rejected')->count(),
        ];
        $pendingReports = StrayReports::where('status', 'pending')->count();
        $investigatingReports = StrayReports::where('status', 'investigating')->count();
        $newUsersToday = User::whereDate('created_at', today())->count();

        // --- Analytical Reports ---

        // 1. Pet Inventory Report
        $petInventory = [
            'total'      => \App\Models\Shared\Pet::count(),
            'available'  => \App\Models\Shared\Pet::where('adoption_status', 'available')->count(),
            'in_process' => \App\Models\Shared\Pet::where('adoption_status', 'in_process')->count(),
            'adopted'    => \App\Models\Shared\Pet::where('adoption_status', 'adopted')->count(),
            'avg_stay'   => round(
                \App\Models\Shared\Pet::where('adoption_status', 'adopted')
                    ->join('adoption_applications', 'pets.pet_id', '=', 'adoption_applications.pet_id')
                    ->where('adoption_applications.status', 'approved')
                    ->selectRaw('AVG(DATEDIFF(adoption_applications.updated_at, pets.created_at)) as avg_days')
                    ->value('avg_days') ?? 0,
                1
            ),
        ];

        // 2. Communication & Response Rate Report
        $messages = \DB::table('messages')->get();
        $commReport = [
            'avg_response_time' => $messages->whereNotNull('is_read')->avg(function ($msg) {
                return isset($msg->created_at, $msg->updated_at) ? \Carbon\Carbon::parse($msg->updated_at)->diffInMinutes($msg->created_at) : null;
            }) ? round($messages->whereNotNull('is_read')->avg(function ($msg) {
                return isset($msg->created_at, $msg->updated_at) ? \Carbon\Carbon::parse($msg->updated_at)->diffInMinutes($msg->created_at) : null;
            }), 1) . ' min' : 'N/A',
            'unanswered' => $messages->where('is_read', false)->count(),
            'peak_time'  => $messages->count()
                ? $messages->groupBy(function ($msg) {
                    return \Carbon\Carbon::parse($msg->created_at)->format('H');
                })
                ->sortByDesc(function ($group) {
                    return count($group);
                })->keys()->first() . ':00'
                : 'N/A',
        ];

        // 3. Shelter Reputation & Feedback Report
        $reviews = \DB::table('adopter_reviews')->get();
        $feedbackReport = [
            'avg_rating' => $reviews->avg('rating') ? round($reviews->avg('rating'), 2) : 'N/A',
            'positive'   => $reviews->where('rating', '>=', 4)->count(),
            'negative'   => $reviews->where('rating', '<=', 2)->count(),
        ];

        // 4. Stray Reports Managed
        $strayReports = \App\Models\Shared\StrayReports::all();
        $strayReport = [
            'total'             => $strayReports->count(),
            'top_area'          => $strayReports->count()
                ? $strayReports->groupBy('location')->sortByDesc(function ($group) {
                    return count($group);
                })->keys()->first()
                : 'N/A',
        ];

        // 5. Pet Demographics Report
        $petDemographics = [
            'by_species' => \App\Models\Shared\Pet::select('species', \DB::raw('count(*) as total'))
                ->groupBy('species')->orderByDesc('total')->get(),
            'by_breed' => \App\Models\Shared\Pet::select('breed', \DB::raw('count(*) as total'))
                ->groupBy('breed')->orderByDesc('total')->limit(5)->get(),
            'by_age_group' => [
                '0-2'   => \App\Models\Shared\Pet::whereBetween('age', [0, 2])->count(),
                '3-6'   => \App\Models\Shared\Pet::whereBetween('age', [3, 6])->count(),
                '7-10'  => \App\Models\Shared\Pet::whereBetween('age', [7, 10])->count(),
                '11+'   => \App\Models\Shared\Pet::where('age', '>=', 11)->count(),
            ],
            'by_gender' => \App\Models\Shared\Pet::select('gender', \DB::raw('count(*) as total'))
                ->groupBy('gender')->get(),
            'by_size' => \App\Models\Shared\Pet::select('size', \DB::raw('count(*) as total'))
                ->groupBy('size')->get(),
            'special_needs' => [
                'with'    => \App\Models\Shared\Pet::where('special_needs', 'Yes')->count(),
                'without' => \App\Models\Shared\Pet::where('special_needs', 'No')->count(),
            ],
            'top_breeds' => \App\Models\Shared\Pet::select('breed', \DB::raw('count(*) as total'))
                ->groupBy('breed')->orderByDesc('total')->limit(3)->get(),
            'top_species' => \App\Models\Shared\Pet::select('species', \DB::raw('count(*) as total'))
                ->groupBy('species')->orderByDesc('total')->limit(3)->get(),
        ];

        // 6.7. Rescuer & Shelter Performance Report
        $rescuerVerifications = [
            'approved' => \App\Models\Rescuer\RescuerVerification::where('status', 'approved')->count(),
            'rejected' => \App\Models\Rescuer\RescuerVerification::where('status', 'rejected')->count(),
        ];
        $shelterVerifications = [
            'approved' => \App\Models\Shelter\ShelterVerification::where('status', 'approved')->count(),
            'rejected' => \App\Models\Shelter\ShelterVerification::where('status', 'rejected')->count(),
        ];

        $mostSavedPets = \DB::table('saved_pets')
            ->select('pet_id', \DB::raw('count(*) as total'))
            ->groupBy('pet_id')->orderByDesc('total')->limit(5)->get();

        $usersMostSaved = \DB::table('saved_pets')
            ->select('adopter_id', \DB::raw('count(*) as total'))
            ->groupBy('adopter_id')->orderByDesc('total')->limit(5)->get();

        $savedTrendsByBreed = \DB::table('saved_pets')
            ->join('pets', 'saved_pets.pet_id', '=', 'pets.pet_id')
            ->select('pets.breed', \DB::raw('count(*) as total'))
            ->groupBy('pets.breed')->orderByDesc('total')->limit(5)->get();

        $savedPetsReport = [
            'most_saved_pets' => $mostSavedPets,
            'users_most_saved' => $usersMostSaved,
            'saved_trends_by_breed' => $savedTrendsByBreed,
        ];
        // --- End Analytical Reports ---

        // Payment statistics
        $totalRevenue = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')->sum('total_amount');
        $totalCommission = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')->sum('pawmatch_commission');
        $pendingPayouts = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->sum('provider_amount');
        $successRate = $this->calculatePaymentSuccessRate();

        // Recent transactions
        $recentTransactions = \App\Models\Shared\MayaTransaction::with(['application.pet', 'shelter', 'rescuer', 'adopter.user'])
            ->orderBy('payment_date', 'desc')
            ->limit(5)
            ->get();

        // Monthly revenue data
        $monthlyRevenue = $this->getMonthlyRevenue();

        // Payout summary
        $payoutSummary = $this->getPayoutSummary();

        $rescuerShelterPerformance = [
            'rescuer_verifications' => $rescuerVerifications,
            'shelter_verifications' => $shelterVerifications,
            // Add other metrics as needed
        ];
        return view('admin.admin_dashboard', compact(
            'totalUsers',
            'pendingReports',
            'investigatingReports',
            'newUsersToday',
            'totalRevenue',
            'totalCommission',
            'pendingPayouts',
            'successRate',
            'recentTransactions',
            'monthlyRevenue',
            'payoutSummary',
            'petInventory',
            'commReport',
            'feedbackReport',
            'strayReport',
            'adoptionPipeline',
            'petDemographics',
            'rescuerShelterPerformance',
            'savedPetsReport'
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
            $query->where(function ($q) use ($search) {
                $q->whereHas('adopter.user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%");
                })
                    ->orWhereHas('pet', function ($q) use ($search) {
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
            $query->where(function ($q) use ($search) {
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhereHas('shelter', function ($q) use ($search) {
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

        $callback = function () use ($users, $columns) {
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
            $query->where(function ($q) use ($search) {
                $q->where('report_id', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        // Filter by flagged status - THIS IS THE NEW PART
        if ($request->filled('flagged')) {
            $flagged = $request->input('flagged');
            if ($flagged === 'flagged') {
                $query->where('is_flagged', true);
            } elseif ($flagged === 'duplicate') {
                $query->where('is_duplicate', true);
            }
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
                ->map(function ($action) {
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

            $oldStatus = $report->status;
            $newStatus = $request->status;

            // Create professional status messages without excessive enthusiasm
            $statusMessages = [
                'pending' => [
                    'investigating' => 'The stray animal report is now being actively investigated by our team. Thank you for your patience.',
                    'resolved' => 'The stray animal report has been successfully resolved. Thank you for helping animals in need.',
                    'cancelled' => 'The stray animal report has been closed after careful review. Thank you for your concern for animal welfare.'
                ],
                'investigating' => [
                    'pending' => 'The stray animal report is back under review. We will keep you updated on any developments.',
                    'resolved' => 'The stray animal report has been successfully resolved. Thank you for making a difference.',
                    'cancelled' => 'The stray animal report has been closed after thorough investigation. Thank you for your dedication to animal welfare.'
                ],
                'resolved' => [
                    'pending' => 'The resolved report is being reviewed again for any additional follow-up needed.',
                    'investigating' => 'The report is being reopened for further investigation. We will keep you informed of our progress.',
                    'cancelled' => 'The report status has been updated. Thank you for your contribution to animal welfare.'
                ],
                'cancelled' => [
                    'pending' => 'The report has been reopened and is now under review.',
                    'investigating' => 'The report is now being actively investigated by our team.',
                    'resolved' => 'The report has been successfully resolved. Thank you for your patience and concern for animals.'
                ]
            ];

            // Debug: Let's add some logging to see what's happening
            \Log::info('Status Update Debug', [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'has_old_status_key' => isset($statusMessages[$oldStatus]),
                'has_new_status_key' => isset($statusMessages[$oldStatus][$newStatus])
            ]);

            // Get the appropriate message with better fallback handling
            $message = null;

            if (isset($statusMessages[$oldStatus]) && isset($statusMessages[$oldStatus][$newStatus])) {
                $message = $statusMessages[$oldStatus][$newStatus];
            } else {
                // Specific fallback messages for each status
                switch ($newStatus) {
                    case 'resolved':
                        $message = 'The stray animal report has been successfully resolved. Thank you for helping animals in need.';
                        break;
                    case 'cancelled':
                        $message = 'The stray animal report has been closed. Thank you for your concern for animal welfare.';
                        break;
                    case 'investigating':
                        $message = 'The stray animal report is now being actively investigated by our team. Thank you for your patience.';
                        break;
                    case 'pending':
                        $message = 'The stray animal report is now under review. We will keep you updated on any developments.';
                        break;
                    default:
                        $message = "The stray animal report status has been updated. Thank you for your commitment to helping animals.";
                }
            }

            // Update the report status
            $report->status = $newStatus;
            $report->save();

            $report->logStatusChange($oldStatus, $newStatus, auth()->id(), $message);

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            \Log::error('Status update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update status'
            ], 500);
        }
    }

    public function strayReportTimeline($id)
    {
        $report = StrayReports::find($id);
        if (!$report) {
            return response()->json(['timeline' => []]);
        }

        $timeline = StrayReportStatusLog::with('changedBy')
            ->where('adopter_id', $report->report_id)
            ->orderBy('changed_at', 'desc')
            ->get()
            ->map(function ($log) {
                return [
                    'date' => $log->changed_at->format('M d, Y g:i A'),
                    'content' => $log->notes,
                    'author' => $log->changedBy ?
                        ($log->changedBy->first_name && $log->changedBy->last_name ?
                            $log->changedBy->first_name . ' ' . $log->changedBy->last_name :
                            'Admin') : 'System',
                    'status_change' => $log->old_status . ' → ' . $log->new_status
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

                    if (
                        strlen($word) > 4 &&
                        !in_array($word, ['street', 'barangay', 'subdivision', 'village', 'city', 'town', 'municipality', 'province'])
                    ) {
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
            $sortedShelters = $shelters->map(function ($shelter) use ($locationKeywords) {
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
            })->sortByDesc(function ($shelter) {
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
                'selected_shelters.*' => 'exists:shelters,shelter_id'
                // Removed 'notification_message' since admin message is removed
            ]);

            $report = StrayReports::findOrFail($reportId);

            if ($report->status === 'investigating') {
                return response()->json([
                    'success' => false,
                    'message' => 'Report is already being investigated'
                ], 400);
            }

            $report->status = 'investigating';
            $report->save();

            // Get shelter names for message
            $shelterNames = \DB::table('shelters')
                ->whereIn('shelter_id', $request->selected_shelters)
                ->pluck('shelter_name')
                ->toArray();

            $shelterList = implode(', ', $shelterNames);

            $message = count($shelterNames) === 1
                ? "The stray animal report is now being handled by {$shelterList}. They have been notified and will work to help the animal you reported. Thank you for caring."
                : "The stray animal report is now being handled by our partner shelters: {$shelterList}. They have been notified and will coordinate to help the animal you reported. Thank you for making a difference.";

            // CREATE NOTIFICATION RECORDS FOR EACH SELECTED SHELTER (without admin_message)
            foreach ($request->selected_shelters as $shelterId) {
                \DB::table('stray_report_notifications')->insert([
                    'report_id' => $report->report_id,
                    'shelter_id' => $shelterId,
                    'sent_at' => now(),
                    'is_read' => false
                ]);
            }

            // Create admin action with professional message
            \DB::table('admin_actions')->insert([
                'action_type' => 'status_update',
                'target_report_id' => $report->report_id,
                'reason' => $message,
                'admin_id' => auth()->id(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Report submitted to shelters successfully'
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

    /**ADDED BY ANDREA - UPDATED FLAG FUNCTIONS */

    public function flagReport(Request $request, $id)
    {
        try {
            $request->validate([
                'flag_reason' => 'required|string|max:500',
                'is_duplicate' => 'boolean'
            ]);

            $report = StrayReports::findOrFail($id);

            // Determine the new status - flagged reports are automatically cancelled
            $newStatus = 'cancelled';  // Always set to cancelled when flagged
            $oldStatus = $report->status;

            $report->update([
                'status' => $newStatus, // Always cancelled when flagged
                'is_flagged' => true,
                'flag_reason' => $request->flag_reason,
                'is_duplicate' => $request->boolean('is_duplicate', false),
                'flagged_by' => 'Admin',
                'flagged_at' => now()
            ]);

            // Add timeline entry for flag action
            DB::table('admin_actions')->insert([
                'action_type' => 'status_update',
                'target_report_id' => $report->report_id,
                'reason' => $request->boolean('is_duplicate', false)
                    ? "Report marked as duplicate and cancelled: {$request->flag_reason}"
                    : "Report flagged and cancelled: {$request->flag_reason}",
                'admin_id' => auth()->id(),
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => $request->boolean('is_duplicate', false)
                    ? 'Report marked as duplicate and cancelled successfully'
                    : 'Report flagged and cancelled successfully',
                'new_status' => $newStatus
            ]);
        } catch (\Exception $e) {
            \Log::error('Flag report error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to flag report: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Show user details for AJAX requests.
     */
    public function showUser(User $user)
    {
        // You can load relationships as needed, e.g. $user->load('adopter', 'shelter', 'rescuer');
        return response()->json($user);
    }

    private function calculatePaymentSuccessRate()
    {
        $total = \App\Models\Shared\MayaTransaction::count();
        $successful = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')->count();

        return $total > 0 ? round(($successful / $total) * 100, 2) : 0;
    }

    private function getMonthlyRevenue()
    {
        $months = [];
        $revenue = [];
        $commission = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->format('M Y');
            $months[] = $monthName;

            $monthRevenue = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('total_amount');

            $monthCommission = \App\Models\Shared\MayaTransaction::where('payment_status', 'paid')
                ->whereYear('payment_date', $date->year)
                ->whereMonth('payment_date', $date->month)
                ->sum('pawmatch_commission');

            $revenue[] = $monthRevenue;
            $commission[] = $monthCommission;
        }

        return [
            'months' => $months,
            'revenue' => $revenue,
            'commission' => $commission
        ];
    }

    private function getPayoutSummary()
    {
        $shelters = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->whereNotNull('shelter_id')
            ->with('shelter')
            ->get()
            ->groupBy('shelter_id')
            ->map(function ($transactions) {
                return (object) [
                    'shelter' => $transactions->first()->shelter,
                    'transaction_count' => $transactions->count(),
                    'total_payout' => $transactions->sum('provider_amount')
                ];
            })
            ->values();

        $rescuers = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->whereNotNull('rescuer_id')
            ->with('rescuer')
            ->get()
            ->groupBy('rescuer_id')
            ->map(function ($transactions) {
                return (object) [
                    'rescuer' => $transactions->first()->rescuer,
                    'transaction_count' => $transactions->count(),
                    'total_payout' => $transactions->sum('provider_amount')
                ];
            })
            ->values();

        return [
            'shelters' => $shelters,
            'rescuers' => $rescuers
        ];
    }

    /**
     * Get payout statistics
     */
    public function getPayoutStats()
    {
        return [
            'total_payouts' => MayaTransaction::where('payout_status', 'completed')->count(),
            'pending_payouts' => MayaTransaction::where('payout_status', 'pending')->count(),
            'failed_payouts' => MayaTransaction::where('payout_status', 'failed')->count(),
            'total_amount_paid' => MayaTransaction::where('payout_status', 'completed')->sum('provider_amount'),
            'pending_amount' => MayaTransaction::where('payout_status', 'pending')->sum('provider_amount'),
        ];
    }

    /**
     * Process manual payout for a transaction
     */
    public function processManualPayout($transactionId)
    {
        try {
            $transaction = MayaTransaction::findOrFail($transactionId);

            // Check if transaction is eligible for payout
            if ($transaction->payment_status !== 'paid') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction payment is not completed'
                ]);
            }

            if ($transaction->payout_status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaction is not eligible for payout'
                ]);
            }

            // Check if disbursement is enabled
            if (!config('maya.disbursement.enabled')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maya disbursement is disabled'
                ]);
            }

            // Check if auto payout is enabled
            if (!config('maya.disbursement.auto_payout')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Auto payout is disabled'
                ]);
            }

            // Get provider details
            $provider = null;
            if ($transaction->shelter_id) {
                $provider = Shelter::find($transaction->shelter_id);
            } elseif ($transaction->rescuer_id) {
                $provider = Rescuer::find($transaction->rescuer_id);
            }

            if (!$provider) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider not found'
                ]);
            }

            // Check bank details
            if (empty($provider->bank_name) || empty($provider->bank_account_number) || empty($provider->bank_account_name)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Provider bank details are incomplete'
                ]);
            }

            // Check payout delay (skip in test mode)
            if (!config('maya.disbursement.test_mode', false)) {
                $payoutDelay = config('maya.disbursement.payout_delay_hours', 24);
                $payoutTime = $transaction->payment_date->addHours($payoutDelay);

                if (now()->lt($payoutTime)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Payout not yet eligible. Available after: " . $payoutTime->format('M d, Y H:i')
                    ]);
                }
            }

            // Process payout using the disbursement service
            $disbursementService = app(\App\Services\MayaDisbursementService::class);
            $result = $disbursementService->processPayout($transaction);

            if ($result) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payout processed successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to process payout - check logs for details'
                ]);
            }
        } catch (\Exception $e) {
            \Log::error("Payout processing error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error processing payout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get pending payouts for admin
     */
    public function getPendingPayouts()
    {
        $pendingPayouts = MayaTransaction::where('payment_status', 'paid')
            ->where('payout_status', 'pending')
            ->with(['application.pet', 'shelter', 'rescuer', 'adopter.user'])
            ->orderBy('payment_date', 'asc')
            ->get();

        return view('admin.pending-payouts', compact('pendingPayouts'));
    }

    public function getUserActivityLogs($userId)
    {
        $logs = \App\Models\ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 logs per page

        return response()->json($logs);
    }

    public function activityLogs($userId)
    {
        $logs = ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 logs per page

        return response()->json($logs);
    }

    public function exportPetsCsv()
    {
        $pets = \App\Models\Shared\Pet::with(['shelter', 'rescuer', 'adoptionApplication.adopter.user'])->get();

        $csvHeader = [
            'Pet ID',
            'Pet Name',
            'Species',
            'Breed',
            'Age',
            'Status',
            'Shelter',
            'Rescuer',
            'Adopted By',
            'Adopter Email'
        ];

        $rows = [];
        foreach ($pets as $pet) {
            $adopter = $pet->adoptionApplication && $pet->adoptionApplication->adopter && $pet->adoptionApplication->adopter->user
                ? $pet->adoptionApplication->adopter->user->first_name . ' ' . $pet->adoptionApplication->adopter->user->last_name
                : '';
            $adopterEmail = $pet->adoptionApplication && $pet->adoptionApplication->adopter && $pet->adoptionApplication->adopter->user
                ? $pet->adoptionApplication->adopter->user->email
                : '';
            $rows[] = [
                $pet->pet_id,
                $pet->name,
                $pet->species,
                $pet->breed,
                $pet->age,
                $pet->adoption_status,
                $pet->shelter->shelter_name ?? '',
                $pet->rescuer->organization_name ?? '',
                $adopter,
                $adopterEmail
            ];
        }

        // Create CSV content
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, $csvHeader);
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }
        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="pets_report.csv"',
        ]);
    }
}
