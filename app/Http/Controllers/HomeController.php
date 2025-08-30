<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Category;
use App\Models\Library;
use App\Models\BookReview;
use App\Models\User;
use App\Models\Loan;
use App\Models\Reservation;
use App\Models\Fine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Display the home page
     */
    public function index()
    {
        // Estatísticas gerais
        $stats = [
            'total_books' => Book::where('status', 'disponivel')->count(),
            'total_users' => User::where('ativo', true)->count(),
            'total_libraries' => Library::where('ativo', true)->count(),
            'active_loans' => Loan::where('status', 'ativo')->count()
        ];

        // Livros mais populares (mais emprestados)
        $popularBooks = Book::select('books.*', DB::raw('COUNT(loans.id) as loans_count'))
            ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
            ->where('books.status', 'disponivel')
            ->where('books.quantidade_disponivel', '>', 0)
            ->groupBy('books.id')
            ->orderByDesc('loans_count')
            ->limit(8)
            ->get();

        // Livros recém-adicionados
        $recentBooks = Book::where('status', 'disponivel')
            ->where('quantidade_disponivel', '>', 0)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Livros mais bem avaliados
        $topRatedBooks = Book::where('status', 'disponivel')
            ->where('quantidade_disponivel', '>', 0)
            ->where('avaliacao_media', '>', 0)
            ->orderByDesc('avaliacao_media')
            ->limit(8)
            ->get();

        // Categorias com mais livros
        $topCategories = Category::select('categories.*', DB::raw('COUNT(books.id) as books_count'))
            ->leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->where('categories.ativo', true)
            ->where('books.status', 'disponivel')
            ->groupBy('categories.id')
            ->orderByDesc('books_count')
            ->limit(6)
            ->get();

        // Avaliações recentes
        $recentReviews = BookReview::with(['user', 'book'])
            ->where('status', 'aprovada')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('index', compact(
            'stats',
            'popularBooks',
            'recentBooks',
            'topRatedBooks',
            'topCategories',
            'recentReviews'
        ));
    }

    /**
     * Display the home page with dashboard content
     */
    public function home()
    {
        // Estatísticas gerais
        $stats = [
            'total_books' => Book::where('status', 'disponivel')->count(),
            'total_users' => User::where('ativo', true)->count(),
            'total_libraries' => Library::where('ativo', true)->count(),
            'active_loans' => Loan::where('status', 'ativo')->count()
        ];

        // Livros mais populares (mais emprestados)
        $popularBooks = Book::select('books.*', DB::raw('COUNT(loans.id) as loans_count'))
            ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
            ->where('books.status', 'disponivel')
            ->where('books.quantidade_disponivel', '>', 0)
            ->groupBy('books.id')
            ->orderByDesc('loans_count')
            ->limit(8)
            ->get();

        // Livros recém-adicionados
        $recentBooks = Book::where('status', 'disponivel')
            ->where('quantidade_disponivel', '>', 0)
            ->orderByDesc('created_at')
            ->limit(8)
            ->get();

        // Livros mais bem avaliados
        $topRatedBooks = Book::where('status', 'disponivel')
            ->where('quantidade_disponivel', '>', 0)
            ->where('avaliacao_media', '>', 0)
            ->orderByDesc('avaliacao_media')
            ->limit(8)
            ->get();

        // Categorias com mais livros
        $topCategories = Category::select('categories.*', DB::raw('COUNT(books.id) as books_count'))
            ->leftJoin('books', 'categories.id', '=', 'books.category_id')
            ->where('categories.ativo', true)
            ->where('books.status', 'disponivel')
            ->groupBy('categories.id')
            ->orderByDesc('books_count')
            ->limit(6)
            ->get();

        // Avaliações recentes
        $recentReviews = BookReview::with(['user', 'book'])
            ->where('status', 'aprovada')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        return view('home', compact(
            'stats',
            'popularBooks',
            'recentBooks',
            'topRatedBooks',
            'topCategories',
            'recentReviews'
        ));
    }

    /**
     * Search books, users, and libraries
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'books');
        $category = $request->get('category');
        $library = $request->get('library');
        $available = $request->boolean('available', false);

        $results = collect();
        $total = 0;

        if (strlen($query) >= 2) {
            switch ($type) {
                case 'books':
                    $booksQuery = Book::with(['category'])
                        ->where('status', 'disponivel')
                        ->where(function ($q) use ($query) {
                            $q->where('titulo', 'like', '%' . $query . '%')
                              ->orWhere('autor', 'like', '%' . $query . '%')
                              ->orWhere('isbn', 'like', '%' . $query . '%')
                              ->orWhere('editora', 'like', '%' . $query . '%');
                        });

                    if ($category) {
                        $booksQuery->where('category_id', $category);
                    }

                    if ($library) {
                        // Note: books table doesn't have library_id, this filter is not applicable
                        // $booksQuery->where('library_id', $library);
                    }

                    if ($available) {
                        $booksQuery->where('quantidade_disponivel', '>', 0);
                    }

                    $total = $booksQuery->count();
                    $results = $booksQuery->orderBy('titulo')
                                         ->paginate(20)
                                         ->withQueryString();
                    break;

                case 'users':
                    if (Auth::check() && Auth::user()->tipo === 'admin') {
                        $usersQuery = User::where('ativo', true)
                            ->where(function ($q) use ($query) {
                                $q->where('name', 'like', '%' . $query . '%')
                                  ->orWhere('email', 'like', '%' . $query . '%')
                                  ->orWhere('cpf', 'like', '%' . $query . '%');
                            });

                        $total = $usersQuery->count();
                        $results = $usersQuery->orderBy('name')
                                             ->paginate(20)
                                             ->withQueryString();
                    }
                    break;

                case 'libraries':
                    $librariesQuery = Library::where('ativo', true)
                        ->where(function ($q) use ($query) {
                            $q->where('nome', 'like', '%' . $query . '%')
                              ->orWhere('endereco', 'like', '%' . $query . '%')
                              ->orWhere('cidade', 'like', '%' . $query . '%');
                        });

                    $total = $librariesQuery->count();
                    $results = $librariesQuery->orderBy('nome')
                                             ->paginate(20)
                                             ->withQueryString();
                    break;
            }
        }

        // Dados para filtros
        $categories = Category::where('ativo', true)
                             ->orderBy('nome')
                             ->get();

        $libraries = Library::where('ativo', true)
                           ->orderBy('nome')
                           ->get();

        return view('search', compact(
            'results',
            'query',
            'type',
            'total',
            'categories',
            'libraries',
            'category',
            'library',
            'available'
        ));
    }

    /**
     * Dashboard for authenticated users
     */
    public function dashboard()
    {
        $user = Auth::user();

        if ($user->tipo === 'admin') {
            return $this->adminDashboard();
        } else {
            return $this->userDashboard();
        }
    }

    /**
     * Admin dashboard
     */
    private function adminDashboard()
    {
        // Estatísticas gerais
        $stats = [
            'total_books' => Book::count(),
            'active_books' => Book::where('status', 'disponivel')->count(),
            'available_books' => Book::where('quantidade_disponivel', '>', 0)->count(),
            'total_users' => User::count(),
            'active_users' => User::where('ativo', true)->count(),
            'total_libraries' => Library::count(),
            'active_libraries' => Library::where('ativo', true)->count(),
            'active_loans' => Loan::where('status', 'ativo')->count(),
            'overdue_loans' => Loan::where('status', 'ativo')
                                  ->where('data_devolucao_prevista', '<', now())
                                  ->count(),
            'active_reservations' => Reservation::where('status', 'ativa')->count(),
            'pending_fines' => Fine::where('status', 'pendente')->count(),
            'pending_reviews' => BookReview::where('status', 'pendente')->count(),
            // Novas estatísticas solicitadas
            'total_clients' => User::where('tipo', 'cliente')->where('ativo', true)->count(),
            'daily_returns' => Loan::where('status', 'devolvido')
                                  ->whereDate('data_devolucao_real', today())
                                  ->count(),
            'reserved_books' => Reservation::where('status', 'ativa')->count()
        ];

        // Empréstimos por mês (últimos 12 meses)
        $loansChart = Loan::select(
                DB::raw('strftime("%Y", created_at) as year'),
                DB::raw('strftime("%m", created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                return [
                    'period' => $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT),
                    'count' => $item->count
                ];
            });

        // Top 10 livros mais lidos (emprestados)
        $topBooks = Book::select('books.*', DB::raw('COUNT(loans.id) as loans_count'))
            ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
            ->groupBy('books.id')
            ->orderByDesc('loans_count')
            ->limit(10)
            ->get();

        // Livro mais lido (para estatística)
        $mostReadBook = Book::select('books.*', DB::raw('COUNT(loans.id) as loans_count'))
            ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
            ->groupBy('books.id')
            ->orderByDesc('loans_count')
            ->first();

        // Adicionar livro mais lido às estatísticas
        $stats['most_read_book'] = $mostReadBook ? $mostReadBook->titulo : 'Nenhum';

        // Clientes cadastrados (usuários ativos)
        $registeredClients = User::where('tipo', 'cliente')
            ->where('ativo', true)
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Devoluções recentes
        $recentReturns = Loan::with(['user', 'book'])
            ->where('status', 'devolvido')
            ->whereNotNull('data_devolucao_real')
            ->orderByDesc('data_devolucao_real')
            ->limit(10)
            ->get();

        // Empréstimos em atraso
        $overdueLoans = Loan::with(['user', 'book'])
            ->where('status', 'ativo')
            ->where('data_devolucao_prevista', '<', now())
            ->orderBy('data_devolucao_prevista')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'loansChart',
            'topBooks',
            'registeredClients',
            'recentReturns',
            'overdueLoans'
        ));
    }

    /**
     * User dashboard
     */
    private function userDashboard()
    {
        $user = Auth::user();

        // Estatísticas do usuário
        $stats = [
            'active_loans' => $user->loans()->where('status', 'ativo')->count(),
            'total_loans' => $user->loans()->count(),
            'active_reservations' => $user->reservations()->where('status', 'ativa')->count(),
            'pending_fines' => $user->fines()->where('status', 'pendente')->sum('valor'),
            'favorite_books' => $user->favorites()->count()
        ];

        // Empréstimos ativos
        $activeLoans = $user->loans()
            ->with(['book', 'book.category'])
            ->where('status', 'ativo')
            ->orderBy('data_devolucao_prevista')
            ->get();

        // Reservas ativas
        $activeReservations = $user->reservations()
            ->with(['book', 'book.category'])
            ->where('status', 'ativa')
            ->orderBy('created_at')
            ->get();

        // Multas pendentes
        $pendingFines = $user->fines()
            ->with(['loan', 'loan.book'])
            ->where('status', 'pendente')
            ->orderByDesc('created_at')
            ->get();

        // Histórico recente
        $recentLoans = $user->loans()
            ->with(['book', 'book.category'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Livros favoritos
        $favoriteBooks = $user->favorites()
            ->with(['book', 'book.category'])
            ->orderByDesc('created_at')
            ->limit(6)
            ->get()
            ->pluck('book');

        // Recomendações baseadas no histórico
        $recommendations = $this->getRecommendations($user);

        return view('dashboard.user', compact(
            'stats',
            'activeLoans',
            'activeReservations',
            'pendingFines',
            'recentLoans',
            'favoriteBooks',
            'recommendations'
        ));
    }

    /**
     * Get book recommendations for user
     */
    private function getRecommendations($user)
    {
        // Categorias dos livros que o usuário já emprestou
        $userCategories = $user->loans()
            ->join('books', 'loans.book_id', '=', 'books.id')
            ->pluck('books.category_id')
            ->unique()
            ->filter();

        if ($userCategories->isEmpty()) {
            // Se não tem histórico, retorna livros populares
            return Book::select('books.*', DB::raw('COUNT(loans.id) as loans_count'))
                ->leftJoin('loans', 'books.id', '=', 'loans.book_id')
                ->where('books.status', 'disponivel')
                ->where('books.quantidade_disponivel', '>', 0)
                ->groupBy('books.id')
                ->orderByDesc('loans_count')
                ->limit(6)
                ->get();
        }

        // Livros das mesmas categorias que o usuário não emprestou
        $borrowedBooks = $user->loans()->pluck('book_id');

        return Book::whereIn('category_id', $userCategories)
            ->whereNotIn('id', $borrowedBooks)
            ->where('status', 'disponivel')
            ->where('quantidade_disponivel', '>', 0)
            ->orderByDesc('avaliacao_media')
            ->limit(6)
            ->get();
    }

    /**
     * About page
     */
    public function about()
    {
        $stats = [
            'total_books' => Book::where('status', 'disponivel')->count(),
            'total_users' => User::where('ativo', true)->count(),
            'total_libraries' => Library::where('ativo', true)->count(),
            'total_loans' => Loan::count()
        ];

        return view('about', compact('stats'));
    }

    /**
     * Contact page
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Privacy policy page
     */
    public function privacy()
    {
        return view('privacy');
    }
}
