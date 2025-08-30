<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Category::withCount('books');

        // Filtros
        if ($request->filled('search')) {
            $query->where('nome', 'like', '%' . $request->search . '%')
                  ->orWhere('descricao', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->where('ativo', false);
            }
        }

        $categories = $query->orderBy('nome')->paginate(15)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        
        return view('categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:categories,nome',
            'descricao' => 'nullable|string|max:500',
            'cor' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'ativo' => 'boolean'
        ]);

        // Gerar slug automaticamente
        $validated['slug'] = Str::slug($validated['nome']);
        $validated['ativo'] = $request->boolean('ativo', true);

        $category = Category::create($validated);

        ActivityLog::logCreate($category);

        return redirect()->route('categories.index')
                        ->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        $category->loadCount('books');
        
        $books = $category->books()
                         ->with('reviews')
                         ->withCount('reviews')
                         ->withAvg(['reviews as average_rating' => function ($query) {
                             $query->where('aprovado', true);
                         }], 'avaliacao')
                         ->orderBy('titulo')
                         ->paginate(12);

        return view('categories.show', compact('category', 'books'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);

        $validated = $request->validate([
            'nome' => 'required|string|max:100|unique:categories,nome,' . $category->id,
            'descricao' => 'nullable|string|max:500',
            'cor' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'ativo' => 'boolean'
        ]);

        $oldValues = $category->getAttributes();

        // Atualizar slug se o nome mudou
        if ($validated['nome'] !== $category->nome) {
            $validated['slug'] = Str::slug($validated['nome']);
        }

        $validated['ativo'] = $request->boolean('ativo', true);

        $category->update($validated);

        ActivityLog::logUpdate($category, $oldValues);

        return redirect()->route('categories.index')
                        ->with('success', 'Categoria atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        // Verificar se a categoria pode ser excluída
        if ($category->books()->exists()) {
            return back()->with('error', 'Não é possível excluir uma categoria que possui livros associados.');
        }

        ActivityLog::logDelete($category);
        
        $category->delete();

        return redirect()->route('categories.index')
                        ->with('success', 'Categoria excluída com sucesso!');
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Category $category)
    {
        $this->authorize('update', $category);

        $oldValues = $category->getAttributes();
        $category->update(['ativo' => !$category->ativo]);

        ActivityLog::logUpdate($category, $oldValues);

        $status = $category->ativo ? 'ativada' : 'desativada';
        
        return back()->with('success', "Categoria {$status} com sucesso!");
    }

    /**
     * Get categories for AJAX requests
     */
    public function getCategories(Request $request)
    {
        $query = $request->get('q');
        
        $categories = Category::active()
                            ->when($query, function ($q) use ($query) {
                                $q->where('nome', 'like', '%' . $query . '%');
                            })
                            ->orderBy('nome')
                            ->limit(10)
                            ->get()
                            ->map(function ($category) {
                                return [
                                    'id' => $category->id,
                                    'text' => $category->nome,
                                    'color' => $category->cor
                                ];
                            });

        return response()->json($categories);
    }
}
