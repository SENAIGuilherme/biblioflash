<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Book;
use App\Models\User;
use App\Models\ActivityLog;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'book', 'book.category']);

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
                case 'expired':
                    $query->expired();
                    break;
                case 'attended':
                    $query->where('status', 'atendida');
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelada');
                    break;
            }
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        $reservations = $query->orderBy('created_at', 'desc')
                             ->paginate(15)
                             ->withQueryString();

        $users = User::clients()->orderBy('name')->get();

        return view('reservations.index', compact('reservations', 'users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $book = null;
        $user = Auth::user();

        if ($request->filled('book_id')) {
            $book = Book::findOrFail($request->book_id);
        }

        $books = Book::with('category')
                    ->where('quantidade_disponivel', 0)
                    ->orderBy('titulo')
                    ->get();

        return view('reservations.create', compact('book', 'user', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'user_id' => 'nullable|exists:users,id'
        ]);

        $book = Book::findOrFail($validated['book_id']);
        $userId = $validated['user_id'] ?? Auth::id();
        $user = User::findOrFail($userId);

        // Verificações de negócio
        if ($book->isAvailable()) {
            return back()->with('error', 'Este livro está disponível para empréstimo. Não é necessário fazer reserva.');
        }

        // Verificar se o usuário já tem reserva ativa para este livro
        if ($user->reservations()->active()->where('book_id', $book->id)->exists()) {
            return back()->with('error', 'Você já possui uma reserva ativa para este livro.');
        }

        // Verificar limite de reservas por usuário
        $maxReservations = SystemSetting::get('max_reservations_per_user', 3);
        if ($user->reservations()->active()->count() >= $maxReservations) {
            return back()->with('error', 'Limite de reservas ativas atingido.');
        }

        // Verificar se usuário pode fazer reservas (sem multas pendentes)
        if (!$user->canBorrowBooks()) {
            return back()->with('error', 'Usuário não pode fazer reservas devido a multas pendentes.');
        }

        $reservation = Reservation::create([
            'user_id' => $userId,
            'book_id' => $book->id,
            'data_reserva' => now(),
            'status' => 'ativa'
        ]);

        ActivityLog::logCreate($reservation);

        $message = Auth::id() === $userId 
            ? 'Reserva realizada com sucesso!' 
            : 'Reserva realizada com sucesso para o usuário!';

        return redirect()->route('reservations.index')
                        ->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'book', 'book.category']);
        
        return view('reservations.show', compact('reservation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reservation $reservation)
    {
        $this->authorize('update', $reservation);
        
        if ($reservation->status !== 'ativa') {
            return back()->with('error', 'Apenas reservas ativas podem ser editadas.');
        }

        return view('reservations.edit', compact('reservation'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== 'ativa') {
            return back()->with('error', 'Apenas reservas ativas podem ser editadas.');
        }

        $validated = $request->validate([
            'observacoes' => 'nullable|string|max:500'
        ]);

        $oldValues = $reservation->getAttributes();
        $reservation->update($validated);

        ActivityLog::logUpdate($reservation, $oldValues);

        return redirect()->route('reservations.show', $reservation)
                        ->with('success', 'Reserva atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reservation $reservation)
    {
        $this->authorize('delete', $reservation);

        if ($reservation->status === 'atendida') {
            return back()->with('error', 'Não é possível excluir uma reserva já atendida.');
        }

        ActivityLog::logDelete($reservation);
        $reservation->delete();

        return redirect()->route('reservations.index')
                        ->with('success', 'Reserva excluída com sucesso!');
    }

    /**
     * Cancel a reservation
     */
    public function cancel(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== 'ativa') {
            return back()->with('error', 'Apenas reservas ativas podem ser canceladas.');
        }

        $oldValues = $reservation->getAttributes();
        $reservation->update([
            'status' => 'cancelada',
            'data_cancelamento' => now()
        ]);

        ActivityLog::logUpdate($reservation, $oldValues);

        return back()->with('success', 'Reserva cancelada com sucesso!');
    }

    /**
     * Attend a reservation (create loan)
     */
    public function attend(Reservation $reservation)
    {
        $this->authorize('update', $reservation);

        if ($reservation->status !== 'ativa') {
            return back()->with('error', 'Apenas reservas ativas podem ser atendidas.');
        }

        if (!$reservation->book->isAvailable()) {
            return back()->with('error', 'Livro não está disponível para empréstimo.');
        }

        // Redirecionar para criação de empréstimo com dados da reserva
        return redirect()->route('loans.create', [
            'reservation_id' => $reservation->id,
            'user_id' => $reservation->user_id,
            'book_id' => $reservation->book_id
        ]);
    }

    /**
     * My reservations (for authenticated user)
     */
    public function myReservations(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->reservations()->with(['book', 'book.category']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'expired':
                    $query->expired();
                    break;
                case 'attended':
                    $query->where('status', 'atendida');
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelada');
                    break;
            }
        }

        $reservations = $query->orderBy('created_at', 'desc')
                             ->paginate(10)
                             ->withQueryString();

        return view('reservations.my-reservations', compact('reservations'));
    }

    /**
     * Queue position for a book
     */
    public function queuePosition(Book $book)
    {
        $activeReservations = $book->reservations()
                                  ->active()
                                  ->orderBy('data_reserva')
                                  ->with('user')
                                  ->get();

        return view('reservations.queue', compact('book', 'activeReservations'));
    }

    /**
     * Expired reservations report
     */
    public function expiredReport()
    {
        $this->authorize('viewAny', Reservation::class);

        $expiredReservations = Reservation::expired()
                                         ->with(['user', 'book'])
                                         ->orderBy('data_expiracao')
                                         ->get();

        return view('reservations.expired-report', compact('expiredReservations'));
    }

    /**
     * Process expired reservations (cron job)
     */
    public function processExpired()
    {
        $expiredCount = Reservation::expired()
                                  ->where('status', 'ativa')
                                  ->update([
                                      'status' => 'expirada',
                                      'data_cancelamento' => now()
                                  ]);

        return response()->json([
            'success' => true,
            'processed' => $expiredCount,
            'message' => "Processadas {$expiredCount} reservas expiradas."
        ]);
    }
}
