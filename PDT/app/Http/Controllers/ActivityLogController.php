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
            $query->where('action', strtoupper($action));
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
        $failedLoginsToday = ActivityLog::whereDate('created_at', $today)->where('action', 'FAILED_LOGIN')->count();
        $uniqueUsersToday = ActivityLog::whereDate('created_at', $today)->whereNotNull('user_id')->distinct('user_id')->count('user_id');

        // Dynamic modules and actions lists for dropdowns
        $availableModules = ActivityLog::select('module')->distinct()->pluck('module')->filter()->values();
        $availableActions = ['CREATED', 'UPDATED', 'DELETED', 'LOGIN', 'FAILED_LOGIN'];

        // 6. Paginate result (latest first)
        $perPage = min((int) $request->input('per_page', 20), 100);
        $logs = $query->latest('id')->paginate($perPage);

        return response()->json([
            'success' => true,
            'stats' => [
                'total_logs' => $totalLogs,
                'today_logs' => $todayLogs,
                'successful_logins_today' => $successfulLoginsToday,
                'failed_logins_today' => $failedLoginsToday,
                'unique_users_today' => $uniqueUsersToday,
            ],
            'filters' => [
                'modules' => $availableModules,
                'actions' => $availableActions,
            ],
            'logs' => $logs->items(),
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

