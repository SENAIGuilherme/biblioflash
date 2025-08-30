<?php

namespace App\Http\Controllers;

use App\Models\Fine;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Fine::class);

        $query = Fine::with(['user', 'loan', 'loan.book']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf', 'like', '%' . $search . '%');
            })->orWhereHas('loan.book', function ($q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                  ->orWhere('autor', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->pending();
                    break;
                case 'paid':
                    $query->paid();
                    break;
                case 'cancelled':
                    $query->cancelled();
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $fines = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->withQueryString();

        $users = User::clients()->orderBy('name')->get();

        $stats = [
            'total_pending' => Fine::pending()->sum('valor'),
            'total_paid' => Fine::paid()->sum('valor'),
            'count_pending' => Fine::pending()->count(),
            'count_paid' => Fine::paid()->count()
        ];

        return view('fines.index', compact('fines', 'users', 'stats'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Fine $fine)
    {
        $this->authorize('view', $fine);

        $fine->load(['user', 'loan', 'loan.book', 'loan.book.category']);

        return view('fines.show', compact('fine'));
    }

    /**
     * Pay a fine
     */
    public function pay(Fine $fine, Request $request)
    {
        $this->authorize('update', $fine);

        if ($fine->status !== 'pendente') {
            return back()->with('error', 'Esta multa não pode ser paga.');
        }

        $validated = $request->validate([
            'observacoes_pagamento' => 'nullable|string|max:500'
        ]);

        DB::transaction(function () use ($fine, $validated) {
            $fine->update([
                'status' => 'paga',
                'data_pagamento' => now(),
                'funcionario_pagamento_id' => Auth::id(),
                'observacoes_pagamento' => $validated['observacoes_pagamento'] ?? null
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'pagamento_multa',
                'model_type' => Fine::class,
                'model_id' => $fine->id,
                'description' => "Multa paga - Valor: R$ {$fine->valor}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });

        return redirect()->route('fines.show', $fine)
                        ->with('success', 'Multa paga com sucesso!');
    }

    /**
     * Cancel a fine
     */
    public function cancel(Fine $fine, Request $request)
    {
        $this->authorize('delete', $fine);

        if ($fine->status !== 'pendente') {
            return back()->with('error', 'Esta multa não pode ser cancelada.');
        }

        $validated = $request->validate([
            'motivo_cancelamento' => 'required|string|max:500'
        ]);

        DB::transaction(function () use ($fine, $validated) {
            $fine->update([
                'status' => 'cancelada',
                'data_cancelamento' => now(),
                'funcionario_cancelamento_id' => Auth::id(),
                'motivo_cancelamento' => $validated['motivo_cancelamento']
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'cancelamento_multa',
                'model_type' => Fine::class,
                'model_id' => $fine->id,
                'description' => "Multa cancelada - Motivo: {$validated['motivo_cancelamento']}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });

        return redirect()->route('fines.show', $fine)
                        ->with('success', 'Multa cancelada com sucesso!');
    }

    /**
     * My fines (for authenticated user)
     */
    public function myFines(Request $request)
    {
        $query = Auth::user()->fines()->with(['loan', 'loan.book']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->pending();
                    break;
                case 'paid':
                    $query->paid();
                    break;
            }
        }

        $fines = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        $stats = [
            'total_pending' => Auth::user()->fines()->pending()->sum('valor'),
            'total_paid' => Auth::user()->fines()->paid()->sum('valor'),
            'count_pending' => Auth::user()->fines()->pending()->count()
        ];

        return view('fines.my-fines', compact('fines', 'stats'));
    }

    /**
     * Financial report
     */
    public function financialReport(Request $request)
    {
        $this->authorize('viewAny', Fine::class);

        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = Fine::whereBetween('created_at', [$startDate, $endDate]);

        $stats = [
            'total_generated' => $query->sum('valor'),
            'total_paid' => $query->paid()->sum('valor'),
            'total_pending' => $query->pending()->sum('valor'),
            'total_cancelled' => $query->cancelled()->sum('valor'),
            'count_generated' => $query->count(),
            'count_paid' => $query->paid()->count(),
            'count_pending' => $query->pending()->count(),
            'count_cancelled' => $query->cancelled()->count()
        ];

        // Dados para gráfico mensal
        $monthlyData = Fine::selectRaw('strftime("%m", created_at) as month, strftime("%Y", created_at) as year, SUM(valor) as total, COUNT(*) as count')
                          ->whereBetween('created_at', [$startDate, $endDate])
                          ->groupBy('year', 'month')
                          ->orderBy('year')
                          ->orderBy('month')
                          ->get();

        // Top usuários com mais multas
        $topUsers = Fine::with('user')
                       ->selectRaw('user_id, SUM(valor) as total_fines, COUNT(*) as count_fines')
                       ->whereBetween('created_at', [$startDate, $endDate])
                       ->groupBy('user_id')
                       ->orderBy('total_fines', 'desc')
                       ->limit(10)
                       ->get();

        return view('fines.financial-report', compact('stats', 'monthlyData', 'topUsers', 'startDate', 'endDate'));
    }
}
