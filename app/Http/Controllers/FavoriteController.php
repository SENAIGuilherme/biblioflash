<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Book;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Display a listing of user's favorite books.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->favorites()->with(['book', 'book.category']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('book', function ($q) use ($search) {
                $q->where('titulo', 'like', '%' . $search . '%')
                  ->orWhere('autor', 'like', '%' . $search . '%')
                  ->orWhere('isbn', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('book', function ($q) use ($request) {
                $q->where('category_id', $request->category);
            });
        }

        if ($request->filled('available')) {
            $query->whereHas('book', function ($q) {
                $q->where('disponivel', true);
            });
        }

        $favorites = $query->orderByDesc('created_at')
                          ->paginate(12)
                          ->withQueryString();

        // Categorias para filtro
        $categories = \App\Models\Category::whereHas('books.favorites', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })->orderBy('nome')->get();

        return view('favorites.index', compact('favorites', 'categories'));
    }

    /**
     * Add a book to favorites.
     */
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id'
        ]);

        $user = Auth::user();
        $bookId = $request->book_id;

        // Verificar se já está nos favoritos
        $exists = Favorite::where('user_id', $user->id)
                         ->where('book_id', $bookId)
                         ->exists();

        if ($exists) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livro já está nos favoritos!'
                ], 400);
            }
            
            return back()->with('error', 'Livro já está nos favoritos!');
        }

        $favorite = Favorite::create([
            'user_id' => $user->id,
            'book_id' => $bookId
        ]);

        // Log da atividade
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'create',
            'model_type' => Favorite::class,
            'model_id' => $favorite->id,
            'description' => 'Livro adicionado aos favoritos',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorite' => true,
                'message' => 'Livro adicionado aos favoritos!'
            ]);
        }

        return back()->with('success', 'Livro adicionado aos favoritos!');
    }

    /**
     * Remove a book from favorites.
     */
    public function destroy(Request $request, $bookId)
    {
        $user = Auth::user();
        
        $favorite = Favorite::where('user_id', $user->id)
                           ->where('book_id', $bookId)
                           ->first();

        if (!$favorite) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Livro não está nos favoritos!'
                ], 404);
            }
            
            return back()->with('error', 'Livro não está nos favoritos!');
        }

        // Log da atividade
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'delete',
            'model_type' => Favorite::class,
            'model_id' => $favorite->id,
            'description' => 'Livro removido dos favoritos',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $favorite->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorite' => false,
                'message' => 'Livro removido dos favoritos!'
            ]);
        }

        return back()->with('success', 'Livro removido dos favoritos!');
    }

    /**
     * Toggle favorite status for a book.
     */
    public function toggle(Request $request, $bookId)
    {
        $user = Auth::user();
        
        $favorite = Favorite::where('user_id', $user->id)
                           ->where('book_id', $bookId)
                           ->first();

        if ($favorite) {
            // Remover dos favoritos
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'delete',
                'model_type' => Favorite::class,
                'model_id' => $favorite->id,
                'description' => 'Livro removido dos favoritos',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $favorite->delete();
            $isFavorite = false;
            $message = 'Livro removido dos favoritos!';
        } else {
            // Adicionar aos favoritos
            $newFavorite = Favorite::create([
                'user_id' => $user->id,
                'book_id' => $bookId
            ]);
            
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'create',
                'model_type' => Favorite::class,
                'model_id' => $newFavorite->id,
                'description' => 'Livro adicionado aos favoritos',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
            
            $isFavorite = true;
            $message = 'Livro adicionado aos favoritos!';
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Clear all favorites for the authenticated user.
     */
    public function clear(Request $request)
    {
        $user = Auth::user();
        
        $count = $user->favorites()->count();
        
        if ($count === 0) {
            return back()->with('info', 'Você não possui livros favoritos.');
        }

        // Log da atividade
        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'bulk_delete',
            'model_type' => Favorite::class,
            'description' => "Todos os favoritos foram removidos ({$count} livros)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $user->favorites()->delete();

        return back()->with('success', "Todos os {$count} livros favoritos foram removidos!");
    }

    /**
     * Export user's favorite books.
     */
    public function export(Request $request)
    {
        $user = Auth::user();
        
        $favorites = $user->favorites()
            ->with(['book', 'book.category'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($favorite) {
                return [
                    'titulo' => $favorite->book->titulo,
                    'autor' => $favorite->book->autor,
                    'categoria' => $favorite->book->category->nome ?? 'Sem categoria',
                    'isbn' => $favorite->book->isbn,
                    'editora' => $favorite->book->editora,
                    'ano_publicacao' => $favorite->book->ano_publicacao,
                    'data_favoritado' => $favorite->created_at->format('d/m/Y H:i:s')
                ];
            });

        $filename = 'meus_favoritos_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($favorites)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Get favorite status for multiple books (AJAX).
     */
    public function status(Request $request)
    {
        $request->validate([
            'book_ids' => 'required|array',
            'book_ids.*' => 'integer|exists:books,id'
        ]);

        $user = Auth::user();
        $bookIds = $request->book_ids;

        $favorites = Favorite::where('user_id', $user->id)
                            ->whereIn('book_id', $bookIds)
                            ->pluck('book_id')
                            ->toArray();

        $status = [];
        foreach ($bookIds as $bookId) {
            $status[$bookId] = in_array($bookId, $favorites);
        }

        return response()->json([
            'success' => true,
            'favorites' => $status
        ]);
    }
}