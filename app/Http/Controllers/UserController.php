<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::withCount(['loans', 'reservations', 'fines']);

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('cpf', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('ativo', false);
            }
        }

        if ($request->filled('city')) {
            $query->where('cidade', 'like', '%' . $request->city . '%');
        }

        $users = $query->orderBy('name')
                      ->paginate(15)
                      ->withQueryString();

        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', User::class);
        
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
            'cpf' => 'nullable|string|size:11|unique:users,cpf',
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'data_nascimento' => 'nullable|date|before:today',
            'tipo' => 'required|in:cliente,bibliotecario,admin',
            'ativo' => 'boolean'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['ativo'] = $request->boolean('ativo', true);

        $user = User::create($validated);

        ActivityLog::logCreate($user);

        return redirect()->route('users.show', $user)
                        ->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        $user->load([
            'loans' => function ($query) {
                $query->with('book')->latest()->limit(5);
            },
            'reservations' => function ($query) {
                $query->with('book')->latest()->limit(5);
            },
            'fines' => function ($query) {
                $query->with('loan.book')->latest()->limit(5);
            }
        ]);

        $user->loadCount(['loans', 'reservations', 'fines']);

        $stats = [
            'active_loans' => $user->getActiveLoansCount(),
            'pending_fines' => $user->getPendingFinesCount(),
            'pending_fines_total' => $user->getPendingFinesTotal(),
            'total_books_borrowed' => $user->loans()->where('status', 'devolvido')->count(),
            'overdue_loans' => $user->loans()->overdue()->count()
        ];

        return view('users.show', compact('user', 'stats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $this->authorize('update', $user);
        
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cpf' => ['nullable', 'string', 'size:11', Rule::unique('users')->ignore($user->id)],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'data_nascimento' => 'nullable|date|before:today',
            'tipo' => 'required|in:cliente,bibliotecario,admin',
            'ativo' => 'boolean'
        ]);

        $oldValues = $user->getAttributes();
        $validated['ativo'] = $request->boolean('ativo', true);

        $user->update($validated);

        ActivityLog::logUpdate($user, $oldValues);

        return redirect()->route('users.show', $user)
                        ->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        // Verificar se o usuário pode ser excluído
        if ($user->loans()->active()->exists()) {
            return back()->with('error', 'Não é possível excluir um usuário com empréstimos ativos.');
        }

        if ($user->reservations()->active()->exists()) {
            return back()->with('error', 'Não é possível excluir um usuário com reservas ativas.');
        }

        if ($user->fines()->pendente()->exists()) {
            return back()->with('error', 'Não é possível excluir um usuário com multas pendentes.');
        }

        ActivityLog::logDelete($user);
        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Usuário excluído com sucesso!');
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        
        $user->load([
            'loans' => function ($query) {
                $query->with('book')->latest()->limit(5);
            },
            'reservations' => function ($query) {
                $query->with('book')->latest()->limit(5);
            },
            'fines' => function ($query) {
                $query->with('loan.book')->latest()->limit(5);
            },
            'favorites.book'
        ]);

        $stats = [
            'active_loans' => $user->getActiveLoansCount(),
            'pending_fines' => $user->getPendingFinesCount(),
            'pending_fines_total' => $user->getPendingFinesTotal(),
            'total_books_borrowed' => $user->loans()->where('status', 'devolvido')->count(),
            'favorite_books' => $user->favorites()->count()
        ];

        return view('profile', compact('user', 'stats'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'cpf' => ['nullable', 'string', 'max:14', Rule::unique('users')->ignore($user->id)],
            'telefone' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:100',
            'estado' => 'nullable|string|max:2',
            'cep' => 'nullable|string|max:10',
            'data_nascimento' => 'nullable|date|before:today'
        ]);

        $oldValues = $user->getAttributes();
        $user->update($validated);

        ActivityLog::logUpdate($user, $oldValues);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Perfil atualizado com sucesso!'
            ]);
        }

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => ['required', 'confirmed', Password::defaults()]
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        ActivityLog::logActivity('password_change', $user, [], [], 'Senha alterada');

        return back()->with('success', 'Senha alterada com sucesso!');
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(User $user)
    {
        $this->authorize('update', $user);

        $oldValues = $user->getAttributes();
        $user->update(['ativo' => !$user->ativo]);

        ActivityLog::logUpdate($user, $oldValues);

        $status = $user->ativo ? 'ativado' : 'desativado';
        
        return back()->with('success', "Usuário {$status} com sucesso!");
    }

    /**
     * User activity history
     */
    public function activityHistory(User $user)
    {
        $this->authorize('view', $user);

        $activities = $user->activityLogs()
                          ->latest()
                          ->paginate(20);

        return view('users.activity-history', compact('user', 'activities'));
    }

    /**
     * Search users (AJAX)
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $users = User::where('name', 'like', '%' . $query . '%')
                    ->orWhere('email', 'like', '%' . $query . '%')
                    ->orWhere('cpf', 'like', '%' . $query . '%')
                    ->active()
                    ->limit(10)
                    ->get()
                    ->map(function ($user) {
                        return [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'tipo' => $user->tipo,
                            'url' => route('users.show', $user)
                        ];
                    });

        return response()->json($users);
    }
}
