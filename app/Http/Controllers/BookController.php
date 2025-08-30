<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\Favorite;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BookController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Verificar se é uma requisição administrativa
        $isAdmin = $request->is('admin/*');
        
        $query = Book::with(['category']);
        
        if ($isAdmin) {
            // Lógica para painel administrativo
            // Filtros
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', '%' . $search . '%')
                      ->orWhere('autor', 'like', '%' . $search . '%')
                      ->orWhere('isbn', 'like', '%' . $search . '%')
                      ->orWhere('editora', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('status')) {
                if ($request->status === 'active') {
                    $query->where('ativo', true);
                } elseif ($request->status === 'inactive') {
                    $query->where('ativo', false);
                }
            }

            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'title':
                        $query->orderBy('titulo');
                        break;
                    case 'author':
                        $query->orderBy('autor');
                        break;
                    case 'year':
                        $query->orderBy('ano_publicacao', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $books = $query->get();
            $categories = Category::orderBy('nome')->get();

            return view('admin.books.index', compact('books', 'categories'));
        } else {
            // Lógica para visualização pública
            $query->available(); // Apenas livros disponíveis
            
            // Filtros públicos
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('titulo', 'like', '%' . $search . '%')
                      ->orWhere('autor', 'like', '%' . $search . '%');
                });
            }

            if ($request->filled('category')) {
                $query->where('category_id', $request->category);
            }

            if ($request->filled('sort')) {
                switch ($request->sort) {
                    case 'title':
                        $query->orderBy('titulo');
                        break;
                    case 'author':
                        $query->orderBy('autor');
                        break;
                    case 'year':
                        $query->orderBy('ano_publicacao', 'desc');
                        break;
                    case 'rating':
                        $query->orderBy('rating', 'desc');
                        break;
                    default:
                        $query->orderBy('created_at', 'desc');
                }
            } else {
                $query->orderBy('created_at', 'desc');
            }

            $books = $query->paginate(12)->withQueryString();
            $categories = Category::active()->orderBy('nome')->get();

            return view('books', compact('books', 'categories'));
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('nome')->get();
        return view('admin.books.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Book::class);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'isbn' => 'nullable|string|unique:books,isbn',
            'editora' => 'nullable|string|max:255',
            'ano_publicacao' => 'nullable|integer|min:1000|max:' . date('Y'),
            'paginas' => 'nullable|integer|min:1',
            'idioma' => 'nullable|string|max:50',
            'sinopse' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'quantidade_total' => 'required|integer|min:1',
            'localizacao' => 'nullable|string|max:100',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean'
        ]);

        // Upload da capa
        if ($request->hasFile('capa')) {
            $validated['capa'] = $request->file('capa')->store('books/covers', 'public');
        }

        $validated['quantidade_disponivel'] = $validated['quantidade_total'];
        $validated['ativo'] = $request->boolean('ativo', true);

        $book = Book::create($validated);

        ActivityLog::logCreate($book);

        return redirect()->route('books.show', $book)
                        ->with('success', 'Livro cadastrado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Book $book)
    {
        // Verificar se é uma requisição administrativa
        if (request()->is('admin/*')) {
            $book->load([
                'category',
                'reviews' => function ($query) {
                    $query->with('user')->latest();
                }
            ]);

            $book->loadCount([
                'reviews',
                'loans',
                'favorites',
                'reservations'
            ]);
            
            $book->loadAvg(['reviews as average_rating' => function ($query) {
                $query->where('aprovado', true);
            }], 'avaliacao');

            return view('admin.books.show', compact('book'));
        }

        // View pública original
        $book->load([
            'category',
            'reviews' => function ($query) {
                $query->approved()->with('user')->latest();
            },
            'reviews.user'
        ]);

        $book->loadCount('reviews');
        $book->loadAvg(['reviews as average_rating' => function ($query) {
            $query->where('aprovado', true);
        }], 'avaliacao');

        $isFavorite = false;
        $userReview = null;
        $canReview = false;

        if (Auth::check()) {
            $user = Auth::user();
            $isFavorite = Favorite::isFavorite($user->id, $book->id);
            $userReview = $book->reviews()->where('user_id', $user->id)->first();
            
            // Usuário pode avaliar se já emprestou o livro e ainda não avaliou
            $canReview = !$userReview && $user->loans()
                ->where('book_id', $book->id)
                ->where('status', 'devolvido')
                ->exists();
        }

        $relatedBooks = Book::where('category_id', $book->category_id)
                           ->where('id', '!=', $book->id)
                           ->available()
                           ->limit(4)
                           ->get();

        return view('books.show', compact('book', 'isFavorite', 'userReview', 'canReview', 'relatedBooks'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Book $book)
    {
        $this->authorize('update', $book);
        
        $categories = Category::active()->orderBy('nome')->get();
        return view('admin.books.edit', compact('book', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Book $book)
    {
        $this->authorize('update', $book);

        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'isbn' => ['nullable', 'string', Rule::unique('books')->ignore($book->id)],
            'editora' => 'nullable|string|max:255',
            'ano_publicacao' => 'nullable|integer|min:1000|max:' . date('Y'),
            'paginas' => 'nullable|integer|min:1',
            'idioma' => 'nullable|string|max:50',
            'sinopse' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'quantidade_total' => 'required|integer|min:1',
            'localizacao' => 'nullable|string|max:100',
            'capa' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'ativo' => 'boolean'
        ]);

        $oldValues = $book->getAttributes();

        // Upload da nova capa
        if ($request->hasFile('capa')) {
            // Deletar capa antiga
            if ($book->capa) {
                Storage::disk('public')->delete($book->capa);
            }
            $validated['capa'] = $request->file('capa')->store('books/covers', 'public');
        }

        // Ajustar quantidade disponível se a total mudou
        if ($validated['quantidade_total'] != $book->quantidade_total) {
            $difference = $validated['quantidade_total'] - $book->quantidade_total;
            $validated['quantidade_disponivel'] = max(0, $book->quantidade_disponivel + $difference);
        }

        $validated['ativo'] = $request->boolean('ativo', true);

        $book->update($validated);

        ActivityLog::logUpdate($book, $oldValues);

        return redirect()->route('books.show', $book)
                        ->with('success', 'Livro atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Book $book)
    {
        $this->authorize('delete', $book);

        // Verificar se o livro pode ser excluído
        if ($book->loans()->active()->exists()) {
            return back()->with('error', 'Não é possível excluir um livro que possui empréstimos ativos.');
        }

        if ($book->reservations()->active()->exists()) {
            return back()->with('error', 'Não é possível excluir um livro que possui reservas ativas.');
        }

        // Deletar capa
        if ($book->capa) {
            Storage::disk('public')->delete($book->capa);
        }

        ActivityLog::logDelete($book);
        
        $book->delete();

        return redirect()->route('admin.books.index')
                        ->with('success', 'Livro excluído com sucesso!');
    }

    /**
     * Toggle favorite status
     */
    public function toggleFavorite(Book $book)
    {
        $user = Auth::user();
        $isFavorite = Favorite::toggle($user->id, $book->id);

        $message = $isFavorite ? 'Livro adicionado aos favoritos!' : 'Livro removido dos favoritos!';
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $message
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Search books (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $books = Book::search($query)
                    ->available()
                    ->with('category')
                    ->limit(10)
                    ->get()
                    ->map(function ($book) {
                        return [
                            'id' => $book->id,
                            'title' => $book->titulo,
                            'author' => $book->autor,
                            'category' => $book->category->nome,
                            'url' => route('books.show', $book)
                        ];
                    });

        return response()->json($books);
    }

    /**
     * Display the RFID panel for book detection.
     */
    public function panel()
    {
        return view('admin.books.panel');
    }

    /**
     * Find a book by RFID code.
     */
    public function findByRFID($rfid)
    {
        $book = Book::with('category')
            ->where('rfid', $rfid)
            ->first();

        if (!$book) {
            return response()->json([
                'error' => 'Livro não encontrado para este RFID'
            ], 404);
        }

        return response()->json($book);
    }
}
