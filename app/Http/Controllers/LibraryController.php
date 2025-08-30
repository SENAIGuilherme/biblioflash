<?php

namespace App\Http\Controllers;

use App\Models\Library;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Library::class);

        $query = Library::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', '%' . $search . '%')
                  ->orWhere('cidade', 'like', '%' . $search . '%')
                  ->orWhere('estado', 'like', '%' . $search . '%')
                  ->orWhere('endereco', 'like', '%' . $search . '%');
            });
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

        if ($request->filled('state')) {
            $query->where('estado', $request->state);
        }

        $libraries = $query->orderBy('nome')
                          ->paginate(15)
                          ->withQueryString();

        return view('libraries.index', compact('libraries'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Library::class);
        
        return view('libraries.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Library::class);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'horario_funcionamento' => 'nullable|array',
            'horario_funcionamento.*.dia' => 'required|string|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'horario_funcionamento.*.abertura' => 'required|date_format:H:i',
            'horario_funcionamento.*.fechamento' => 'required|date_format:H:i|after:horario_funcionamento.*.abertura',
            'horario_funcionamento.*.fechado' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'ativo' => 'boolean'
        ]);

        $validated['ativo'] = $request->boolean('ativo', true);

        $library = Library::create($validated);

        ActivityLog::logCreate($library);

        return redirect()->route('libraries.show', $library)
                        ->with('success', 'Biblioteca cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Library $library)
    {
        $this->authorize('view', $library);

        return view('libraries.show', compact('library'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Library $library)
    {
        $this->authorize('update', $library);
        
        return view('libraries.edit', compact('library'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Library $library)
    {
        $this->authorize('update', $library);

        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'estado' => 'required|string|size:2',
            'cep' => 'required|string|max:10',
            'telefone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'horario_funcionamento' => 'nullable|array',
            'horario_funcionamento.*.dia' => 'required|string|in:segunda,terca,quarta,quinta,sexta,sabado,domingo',
            'horario_funcionamento.*.abertura' => 'required|date_format:H:i',
            'horario_funcionamento.*.fechamento' => 'required|date_format:H:i|after:horario_funcionamento.*.abertura',
            'horario_funcionamento.*.fechado' => 'boolean',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'ativo' => 'boolean'
        ]);

        $library->update($validated);

        ActivityLog::logUpdate($library);

        return redirect()->route('libraries.show', $library)
                        ->with('success', 'Biblioteca atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Library $library)
    {
        $this->authorize('delete', $library);

        ActivityLog::logDelete($library);
        
        $library->delete();

        return redirect()->route('libraries.index')
                        ->with('success', 'Biblioteca removida com sucesso!');
    }

    /**
     * Toggle library status
     */
    public function toggleStatus(Library $library)
    {
        $this->authorize('update', $library);

        $library->update(['ativo' => !$library->ativo]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'toggle_status',
            'model_type' => Library::class,
            'model_id' => $library->id,
            'description' => "Status da biblioteca alterado para: " . ($library->ativo ? 'Ativo' : 'Inativo'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);

        $status = $library->ativo ? 'ativada' : 'desativada';
        return back()->with('success', "Biblioteca {$status} com sucesso!");
    }

    /**
     * Show library map
     */
    public function map()
    {
        $libraries = Library::active()
                           ->whereNotNull('latitude')
                           ->whereNotNull('longitude')
                           ->get();

        return view('libraries.map', compact('libraries'));
    }

    /**
     * Get libraries for AJAX requests
     */
    public function getLibraries(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $libraries = Library::where('nome', 'like', '%' . $query . '%')
                           ->orWhere('cidade', 'like', '%' . $query . '%')
                           ->active()
                           ->limit(10)
                           ->get()
                           ->map(function ($library) {
                               return [
                                   'id' => $library->id,
                                   'nome' => $library->nome,
                                   'cidade' => $library->cidade,
                                   'endereco' => $library->endereco,
                                   'telefone' => $library->telefone,
                                   'url' => route('libraries.show', $library)
                               ];
                           });

        return response()->json($libraries);
    }
}