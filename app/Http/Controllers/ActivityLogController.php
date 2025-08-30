<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of activity logs.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with(['user']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', '%' . $search . '%')
                  ->orWhere('action', 'like', '%' . $search . '%')
                  ->orWhere('model_type', 'like', '%' . $search . '%')
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                               ->orWhere('email', 'like', '%' . $search . '%');
                  });
            });
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('period')) {
            switch ($request->period) {
                case 'today':
                    $query->whereDate('created_at', today());
                    break;
                case 'yesterday':
                    $query->whereDate('created_at', yesterday());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->startOfWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->startOfMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->startOfYear());
                    break;
            }
        }

        $logs = $query->orderByDesc('created_at')
                     ->paginate(25)
                     ->withQueryString();

        // Dados para filtros
        $users = User::orderBy('name')->get();
        $actions = ActivityLog::distinct('action')
                             ->orderBy('action')
                             ->pluck('action');
        $modelTypes = ActivityLog::distinct('model_type')
                                ->whereNotNull('model_type')
                                ->orderBy('model_type')
                                ->pluck('model_type')
                                ->map(function ($type) {
                                    return class_basename($type);
                                })
                                ->unique()
                                ->sort();

        return view('activity-logs.index', compact('logs', 'users', 'actions', 'modelTypes'));
    }

    /**
     * Display the specified activity log.
     */
    public function show(ActivityLog $activityLog)
    {
        $this->authorize('view', $activityLog);

        $activityLog->load(['user']);

        return view('activity-logs.show', compact('activityLog'));
    }

    /**
     * Display activity logs for a specific user.
     */
    public function userLogs(Request $request, User $user)
    {
        $this->authorize('view', $user);

        $query = $user->activityLogs();

        // Filtros
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('model_type')) {
            $query->where('model_type', $request->model_type);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')
                     ->paginate(20)
                     ->withQueryString();

        $actions = $user->activityLogs()
                       ->distinct('action')
                       ->orderBy('action')
                       ->pluck('action');

        $modelTypes = $user->activityLogs()
                          ->distinct('model_type')
                          ->whereNotNull('model_type')
                          ->orderBy('model_type')
                          ->pluck('model_type')
                          ->map(function ($type) {
                              return class_basename($type);
                          })
                          ->unique()
                          ->sort();

        return view('activity-logs.user-logs', compact('user', 'logs', 'actions', 'modelTypes'));
    }

    /**
     * Display activity statistics.
     */
    public function statistics(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $period = $request->get('period', 'month'); // day, week, month, year
        $startDate = $this->getStartDate($period);

        // Estatísticas gerais
        $stats = [
            'total_activities' => ActivityLog::where('created_at', '>=', $startDate)->count(),
            'unique_users' => ActivityLog::where('created_at', '>=', $startDate)
                                        ->distinct('user_id')
                                        ->count('user_id'),
            'most_active_user' => $this->getMostActiveUser($startDate),
            'most_common_action' => $this->getMostCommonAction($startDate)
        ];

        // Atividades por dia
        $dailyActivities = ActivityLog::selectRaw('strftime("%Y-%m-%d", created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Atividades por ação
        $actionStats = ActivityLog::selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Atividades por modelo
        $modelStats = ActivityLog::selectRaw('model_type, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->whereNotNull('model_type')
            ->groupBy('model_type')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                $item->model_name = class_basename($item->model_type);
                return $item;
            });

        // Usuários mais ativos
        $topUsers = ActivityLog::with('user')
            ->selectRaw('user_id, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Atividades recentes
        $recentActivities = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('activity-logs.statistics', compact(
            'stats',
            'dailyActivities',
            'actionStats',
            'modelStats',
            'topUsers',
            'recentActivities',
            'period'
        ));
    }

    /**
     * Export activity logs.
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', ActivityLog::class);

        $query = ActivityLog::with(['user']);

        // Aplicar filtros se fornecidos
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderByDesc('created_at')
                     ->limit(5000) // Limitar para evitar problemas de memória
                     ->get()
                     ->map(function ($log) {
                         return [
                             'id' => $log->id,
                             'usuario' => $log->user ? $log->user->name : 'Sistema',
                             'acao' => $log->action_label,
                             'modelo' => $log->model_type ? class_basename($log->model_type) : null,
                             'modelo_id' => $log->model_id,
                             'descricao' => $log->description,
                             'ip' => $log->ip_address,
                             'user_agent' => $log->user_agent,
                             'data_hora' => $log->created_at->format('d/m/Y H:i:s')
                         ];
                     });

        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($logs)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Clean old activity logs.
     */
    public function cleanup(Request $request)
    {
        $this->authorize('delete', ActivityLog::class);

        $request->validate([
            'days' => 'required|integer|min:30|max:365'
        ]);

        $days = $request->days;
        $cutoffDate = now()->subDays($days);

        $count = ActivityLog::where('created_at', '<', $cutoffDate)->count();
        
        if ($count === 0) {
            return back()->with('info', 'Não há logs antigos para remover.');
        }

        ActivityLog::where('created_at', '<', $cutoffDate)->delete();

        // Log da limpeza
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'cleanup',
            'description' => "Limpeza de logs: {$count} registros removidos (mais antigos que {$days} dias)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return back()->with('success', "Limpeza concluída: {$count} logs antigos foram removidos.");
    }

    /**
     * Get start date based on period.
     */
    private function getStartDate(string $period): Carbon
    {
        switch ($period) {
            case 'day':
                return now()->startOfDay();
            case 'week':
                return now()->startOfWeek();
            case 'year':
                return now()->startOfYear();
            case 'month':
            default:
                return now()->startOfMonth();
        }
    }

    /**
     * Get most active user in period.
     */
    private function getMostActiveUser(Carbon $startDate)
    {
        return ActivityLog::with('user')
            ->selectRaw('user_id, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('user_id')
            ->orderByDesc('count')
            ->first();
    }

    /**
     * Get most common action in period.
     */
    private function getMostCommonAction(Carbon $startDate)
    {
        return ActivityLog::selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', $startDate)
            ->groupBy('action')
            ->orderByDesc('count')
            ->first();
    }
}