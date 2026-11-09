<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Get paginated activity logs with search, filters, and summary statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $query = ActivityLog::query();

        // 1. Search Query (Keyword)
        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('user_name', 'like', "%{$search}%")
                  ->orWhere('ip_address', 'like', "%{$search}%")
                  ->orWhere('device_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('target_id', 'like', "%{$search}%");
            });
        }

        // 2. Filter by Module
        if ($module = $request->input('module')) {
            $query->where('module', $module);
        }

        // 3. Filter by Action
        if ($action = $request->input('action')) {
            $upperAction = strtoupper(trim($action));
            if ($upperAction === 'SESSIONS' || $upperAction === 'AUTH') {
                $query->whereIn('action', ['LOGIN', 'LOGOUT']);
            } else {
                $query->where('action', $upperAction);
            }
        }

        // 4. Filter by Date (Preset or Range)
        $datePreset = $request->input('date_preset', 'all');
        $now = Carbon::now();

        if ($datePreset === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($datePreset === 'yesterday') {
            $query->whereDate('created_at', Carbon::yesterday());
        } elseif ($datePreset === 'week') {
            $query->where('created_at', '>=', $now->copy()->subDays(7)->startOfDay());
        } elseif ($datePreset === 'month') {
            $query->where('created_at', '>=', $now->copy()->startOfMonth());
        } elseif ($datePreset === 'custom') {
            if ($dateFrom = $request->input('date_from')) {
                $query->where('created_at', '>=', Carbon::parse($dateFrom)->startOfDay());
            }
            if ($dateTo = $request->input('date_to')) {
                $query->where('created_at', '<=', Carbon::parse($dateTo)->endOfDay());
            }
        }

        // 5. Summary Stats
        $today = Carbon::today();
        $totalLogs = ActivityLog::count();
        $todayLogs = ActivityLog::whereDate('created_at', $today)->count();
        $successfulLoginsToday = ActivityLog::whereDate('created_at', $today)->where('action', 'LOGIN')->count();
        $logoutsToday = ActivityLog::whereDate('created_at', $today)->where('action', 'LOGOUT')->count();
        $failedLoginsToday = ActivityLog::whereDate('created_at', $today)->where('action', 'FAILED_LOGIN')->count();
        $uniqueUsersToday = ActivityLog::whereDate('created_at', $today)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // Dynamic modules and actions lists for dropdowns
        $availableModules = ActivityLog::select('module')->distinct()->pluck('module')->filter()->values();
        $availableActions = ['CREATED', 'UPDATED', 'DELETED', 'LOGIN', 'LOGOUT', 'FAILED_LOGIN'];

        // 6. Paginate result (latest first)
        $perPage = min((int) $request->input('per_page', 20), 100);
        $logs = $query->latest('id')->paginate($perPage);

        // Fetch User and Student/Teacher Profiles to resolve exact user names
        $userIds = collect($logs->items())->pluck('user_id')->filter()->unique();
        $usersMap = \App\Models\User::with([
            'student:id,user_id,name_khmer,name_latin,student_id_card,grade',
            'teacher:id,user_id,name_khmer,name_latin'
        ])->whereIn('id', $userIds)->get()->keyBy('id');

        // Enrich items with formatted session timing details and verified user names
        $transformedLogs = collect($logs->items())->map(function ($item) use ($usersMap) {
            $user = $item->user_id ? $usersMap->get($item->user_id) : null;
            
            $resolvedName = null;
            $userEmail = $user?->email;
            $studentCard = null;
            $studentGrade = null;

            if ($user) {
                if ($user->student && !empty($user->student->name_khmer)) {
                    $resolvedName = $user->student->name_khmer;
                    $studentCard = $user->student->student_id_card;
                    $studentGrade = $user->student->grade;
                } elseif ($user->teacher && !empty($user->teacher->name_khmer)) {
                    $resolvedName = 'គ្រូ ' . $user->teacher->name_khmer;
                } elseif (!empty($user->name) && !in_array(strtolower($user->name), ['student', 'teacher', 'user'])) {
                    $resolvedName = $user->name;
                }
            }

            $displayName = $resolvedName ?: ($item->user_name ?: 'ភ្ញៀវ (Guest)');
            $item->display_name = $displayName;
            $item->user_email = $userEmail;
            $item->student_card = $studentCard;
            $item->student_grade = $studentGrade;

            $payload = is_array($item->payload) ? $item->payload : json_decode($item->payload ?? '[]', true);
            $newVals = $payload['new_values'] ?? [];

            $sessionInfo = null;
            if ($item->action === 'LOGOUT') {
                $sessionInfo = [
                    'type' => 'LOGOUT',
                    'login_time' => $newVals['login_time'] ?? null,
                    'logout_time' => $newVals['logout_time'] ?? Carbon::parse($item->created_at)->format('H:i'),
                    'duration' => $newVals['duration_text'] ?? null,
                    'duration_minutes' => $newVals['duration_minutes'] ?? null,
                    'date' => $newVals['logout_date'] ?? Carbon::parse($item->created_at)->format('d/m/Y'),
                ];
            } elseif ($item->action === 'LOGIN') {
                $sessionInfo = [
                    'type' => 'LOGIN',
                    'login_time' => $newVals['login_time'] ?? Carbon::parse($item->created_at)->format('H:i'),
                    'logout_time' => null,
                    'duration' => null,
                    'date' => $newVals['login_date'] ?? Carbon::parse($item->created_at)->format('d/m/Y'),
                ];
            }

            $item->session_info = $sessionInfo;
            return $item;
        });

        return response()->json([
            'success' => true,
            'stats' => [
                'total_logs' => $totalLogs,
                'today_logs' => $todayLogs,
                'successful_logins_today' => $successfulLoginsToday,
                'logouts_today' => $logoutsToday,
                'failed_logins_today' => $failedLoginsToday,
                'unique_users_today' => $uniqueUsersToday,
            ],
            'filters' => [
                'modules' => $availableModules,
                'actions' => $availableActions,
            ],
            'logs' => $transformedLogs,
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'from' => $logs->firstItem(),
                'to' => $logs->lastItem(),
            ],
        ]);
    }
}

