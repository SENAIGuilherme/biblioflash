<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Book;
use App\Models\User;
use App\Models\Reservation;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Loan::with(['user', 'book', 'book.category']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            })->orWhereHas('book', function ($q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                  ->orWhere('autor', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
                case 'returned':
                    $query->where('status', 'devolvido');
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $loans = $query->orderBy('created_at', 'desc')
                      ->paginate(15)
                      ->withQueryString();

        $users = User::clients()->orderBy('name')->get();

        return view('loans.index', compact('loans', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $this->authorize('create', Loan::class);
        
        $book = null;
        $user = null;
        $reservation = null;

        if ($request->filled('book_id')) {
            $book = Book::findOrFail($request->book_id);
        }

        if ($request->filled('user_id')) {
            $user = User::findOrFail($request->user_id);
        }

        if ($request->filled('reservation_id')) {
            $reservation = Reservation::with(['user', 'book'])->findOrFail($request->reservation_id);
            $book = $reservation->book;
            $user = $reservation->user;
        }

        $users = User::clients()->orderBy('name')->get();
        $books = Book::available()->with('category')->orderBy('titulo')->get();

        return view('loans.create', compact('book', 'user', 'reservation', 'users', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Loan::class);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'book_id' => 'required|exists:books,id',
            'reservation_id' => 'nullable|exists:reservations,id',
            'observacoes' => 'nullable|string|max:500'
        ]);

        $user = User::findOrFail($validated['user_id']);
        $book = Book::findOrFail($validated['book_id']);

        // Verificações de negócio
        if (!$user->canBorrowBooks()) {
            return back()->with('error', 'Usuário não pode realizar empréstimos (multas pendentes ou limite atingido).');
        }

        if (!$book->isAvailable()) {
            return back()->with('error', 'Livro não está disponível para empréstimo.');
        }

        DB::transaction(function () use ($validated, $user, $book) {
            // Criar empréstimo
            $loan = Loan::create([
                'user_id' => $validated['user_id'],
                'book_id' => $validated['book_id'],
                'reservation_id' => $validated['reservation_id'] ?? null,
                'data_emprestimo' => now(),
                'observacoes' => $validated['observacoes'],
                'funcionario_id' => Auth::id()
            ]);

            // Atualizar quantidade disponível do livro
            $book->decrement('quantidade_disponivel');

            // Se há reserva, marcar como atendida
            if ($validated['reservation_id']) {
                $reservation = Reservation::find($validated['reservation_id']);
                $reservation->update([
                    'status' => 'atendida',
                    'data_atendimento' => now()
                ]);
            }

            ActivityLog::logCreate($loan);
        });

        return redirect()->route('loans.index')
                        ->with('success', 'Empréstimo realizado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Loan $loan)
    {
        $loan->load(['user', 'book', 'book.category', 'reservation', 'fines']);
        
        return view('loans.show', compact('loan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Loan $loan)
    {
        $this->authorize('update', $loan);
        
        if ($loan->status === 'devolvido') {
            return back()->with('error', 'Não é possível editar um empréstimo já devolvido.');
        }

        return view('loans.edit', compact('loan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Loan $loan)
    {
        $this->authorize('update', $loan);

        if ($loan->status === 'devolvido') {
            return back()->with('error', 'Não é possível editar um empréstimo já devolvido.');
        }

        $validated = $request->validate([
            'data_prevista_devolucao' => 'required|date|after:today',
            'observacoes' => 'nullable|string|max:500'
        ]);

        $oldValues = $loan->getAttributes();
        $loan->update($validated);

        ActivityLog::logUpdate($loan, $oldValues);

        return redirect()->route('loans.show', $loan)
                        ->with('success', 'Empréstimo atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Loan $loan)
    {
        $this->authorize('delete', $loan);

        if ($loan->status === 'devolvido') {
            return back()->with('error', 'Não é possível excluir um empréstimo já devolvido.');
        }

        DB::transaction(function () use ($loan) {
            // Restaurar quantidade disponível do livro
            $loan->book->increment('quantidade_disponivel');

            ActivityLog::logDelete($loan);
            $loan->delete();
        });

        return redirect()->route('loans.index')
                        ->with('success', 'Empréstimo cancelado com sucesso!');
    }

    /**
     * Return a book
     */
    public function return(Loan $loan)
    {
        $this->authorize('update', $loan);

        if ($loan->status === 'devolvido') {
            return back()->with('error', 'Este empréstimo já foi devolvido.');
        }

        DB::transaction(function () use ($loan) {
            $loan->returnBook();
            ActivityLog::logActivity('devolucao', $loan, [], [], 'Livro devolvido');
        });

        return back()->with('success', 'Livro devolvido com sucesso!');
    }

    /**
     * Renew a loan
     */
    public function renew(Loan $loan)
    {
        $this->authorize('update', $loan);

        if ($loan->status === 'devolvido') {
            return back()->with('error', 'Não é possível renovar um empréstimo já devolvido.');
        }

        if ($loan->isOverdue()) {
            return back()->with('error', 'Não é possível renovar um empréstimo em atraso.');
        }

        $maxRenewals = SystemSetting::get('max_renewals', 2);
        if ($loan->renovacoes >= $maxRenewals) {
            return back()->with('error', 'Limite de renovações atingido.');
        }

        // Verificar se há reservas para este livro
        if ($loan->book->reservations()->active()->exists()) {
            return back()->with('error', 'Não é possível renovar. Há reservas pendentes para este livro.');
        }

        DB::transaction(function () use ($loan) {
            $loan->renew();
            ActivityLog::logActivity('renovacao', $loan, [], [], 'Empréstimo renovado');
        });

        return back()->with('success', 'Empréstimo renovado com sucesso!');
    }

    /**
     * My loans (for authenticated user)
     */
    public function myLoans(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->loans()->with(['book', 'book.category', 'fines']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'overdue':
                    $query->overdue();
                    break;
                case 'returned':
                    $query->where('status', 'devolvido');
                    break;
            }
        }

        $loans = $query->orderBy('created_at', 'desc')
                      ->paginate(10)
                      ->withQueryString();

        return view('loans.my-loans', compact('loans'));
    }

    /**
     * Generate overdue report
     */
    public function overdueReport()
    {
        $this->authorize('viewAny', Loan::class);

        $overdueLoans = Loan::overdue()
                           ->with(['user', 'book', 'fines'])
                           ->orderBy('data_prevista_devolucao')
                           ->get();

        return view('loans.overdue-report', compact('overdueLoans'));
    }
}
