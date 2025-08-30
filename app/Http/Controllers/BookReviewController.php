<?php

namespace App\Http\Controllers;

use App\Models\BookReview;
use App\Models\Book;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', BookReview::class);

        $query = BookReview::with(['user', 'book']);

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
                case 'pending':
                    $query->where('aprovado', false);
                    break;
                case 'approved':
                    $query->where('aprovado', true);
                    break;
            }
        }

        if ($request->filled('rating')) {
            $query->where('avaliacao', $request->rating);
        }

        if ($request->filled('book_id')) {
            $query->where('book_id', $request->book_id);
        }

        $reviews = $query->orderBy('created_at', 'desc')
                        ->paginate(15)
                        ->withQueryString();

        $books = Book::orderBy('titulo')->get();

        return view('book-reviews.index', compact('reviews', 'books'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'book_id' => 'required|exists:books,id',
            'avaliacao' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000'
        ]);

        $book = Book::findOrFail($validated['book_id']);

        // Verificar se o usuário já avaliou este livro
        $existingReview = BookReview::where('user_id', Auth::id())
                                   ->where('book_id', $validated['book_id'])
                                   ->first();

        if ($existingReview) {
            return back()->with('error', 'Você já avaliou este livro.');
        }

        // Verificar se o usuário já emprestou este livro
        $hasLoanedBook = Auth::user()->loans()
                            ->where('book_id', $validated['book_id'])
                            ->where('status', 'devolvido')
                            ->exists();

        if (!$hasLoanedBook) {
            return back()->with('error', 'Você só pode avaliar livros que já emprestou.');
        }

        $review = BookReview::create([
            'user_id' => Auth::id(),
            'book_id' => $validated['book_id'],
            'avaliacao' => $validated['avaliacao'],
            'comentario' => $validated['comentario'],
            'aprovado' => false // Precisa de aprovação
        ]);

        ActivityLog::logCreate($review);

        return back()->with('success', 'Avaliação enviada com sucesso! Aguarde aprovação.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BookReview $bookReview)
    {
        $this->authorize('view', $bookReview);

        $bookReview->load(['user', 'book', 'book.category']);

        return view('book-reviews.show', compact('bookReview'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BookReview $bookReview)
    {
        $this->authorize('update', $bookReview);

        $validated = $request->validate([
            'avaliacao' => 'required|integer|min:1|max:5',
            'comentario' => 'nullable|string|max:1000'
        ]);

        $bookReview->update($validated);

        ActivityLog::logUpdate($bookReview);

        return redirect()->route('book-reviews.show', $bookReview)
                        ->with('success', 'Avaliação atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BookReview $bookReview)
    {
        $this->authorize('delete', $bookReview);

        ActivityLog::logDelete($bookReview);
        
        $bookReview->delete();

        return redirect()->route('book-reviews.index')
                        ->with('success', 'Avaliação removida com sucesso!');
    }

    /**
     * Approve a review
     */
    public function approve(BookReview $bookReview)
    {
        $this->authorize('update', $bookReview);

        if ($bookReview->aprovado) {
            return back()->with('error', 'Esta avaliação já foi aprovada.');
        }

        DB::transaction(function () use ($bookReview) {
            $bookReview->update(['aprovado' => true]);

            // Recalcular média de avaliações do livro
            $book = $bookReview->book;
            $averageRating = $book->reviews()->where('aprovado', true)->avg('avaliacao');
            $book->update(['avaliacao_media' => $averageRating]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'aprovacao_avaliacao',
                'model_type' => BookReview::class,
                'model_id' => $bookReview->id,
                'description' => "Avaliação aprovada para o livro: {$book->titulo}",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        });

        return back()->with('success', 'Avaliação aprovada com sucesso!');
    }

    /**
     * Reject a review
     */
    public function reject(BookReview $bookReview, Request $request)
    {
        $this->authorize('delete', $bookReview);

        if ($bookReview->aprovado) {
            return back()->with('error', 'Esta avaliação já foi aprovada e não pode ser rejeitada.');
        }

        $validated = $request->validate([
            'motivo_rejeicao' => 'required|string|max:500'
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'rejeicao_avaliacao',
            'model_type' => BookReview::class,
            'model_id' => $bookReview->id,
            'description' => "Avaliação rejeitada - Motivo: {$validated['motivo_rejeicao']}",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $bookReview->delete();

        return back()->with('success', 'Avaliação rejeitada e removida.');
    }

    /**
     * My reviews (for authenticated user)
     */
    public function myReviews(Request $request)
    {
        $query = Auth::user()->reviews()->with(['book', 'book.category']);

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'pending':
                    $query->where('aprovado', false);
                    break;
                case 'approved':
                    $query->where('aprovado', true);
                    break;
            }
        }

        $reviews = $query->orderBy('created_at', 'desc')
                        ->paginate(10)
                        ->withQueryString();

        return view('book-reviews.my-reviews', compact('reviews'));
    }
}