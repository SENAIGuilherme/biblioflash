<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class SystemSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', SystemSetting::class);

        $query = SystemSetting::query();

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhere('group', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $settings = $query->orderBy('group')
                         ->orderBy('key')
                         ->paginate(20)
                         ->withQueryString();

        $groups = SystemSetting::distinct('group')
                              ->orderBy('group')
                              ->pluck('group');

        $types = SystemSetting::distinct('type')
                             ->orderBy('type')
                             ->pluck('type');

        return view('system-settings.index', compact('settings', 'groups', 'types'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', SystemSetting::class);
        
        $groups = SystemSetting::distinct('group')
                              ->orderBy('group')
                              ->pluck('group');

        return view('system-settings.create', compact('groups'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', SystemSetting::class);

        $validated = $request->validate([
            'key' => 'required|string|max:255|unique:system_settings,key',
            'value' => 'required|string',
            'type' => 'required|in:string,integer,boolean,decimal,json,text',
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100',
            'is_public' => 'boolean'
        ]);

        // Validar valor baseado no tipo
        $this->validateValueByType($validated['value'], $validated['type']);

        $validated['is_public'] = $request->boolean('is_public', false);

        $setting = SystemSetting::create($validated);

        // Limpar cache
        Cache::forget('system_settings');
        Cache::forget('public_system_settings');

        ActivityLog::logCreate($setting);

        return redirect()->route('system-settings.show', $setting)
                        ->with('success', 'Configuração criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(SystemSetting $systemSetting)
    {
        $this->authorize('view', $systemSetting);

        return view('system-settings.show', compact('systemSetting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SystemSetting $systemSetting)
    {
        $this->authorize('update', $systemSetting);
        
        $groups = SystemSetting::distinct('group')
                              ->orderBy('group')
                              ->pluck('group');

        return view('system-settings.edit', compact('systemSetting', 'groups'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SystemSetting $systemSetting)
    {
        $this->authorize('update', $systemSetting);

        $validated = $request->validate([
            'key' => [
                'required',
                'string',
                'max:255',
                Rule::unique('system_settings', 'key')->ignore($systemSetting->id)
            ],
            'value' => 'required|string',
            'type' => 'required|in:string,integer,boolean,decimal,json,text',
            'description' => 'nullable|string|max:500',
            'group' => 'required|string|max:100',
            'is_public' => 'boolean'
        ]);

        // Validar valor baseado no tipo
        $this->validateValueByType($validated['value'], $validated['type']);

        $systemSetting->update($validated);

        // Limpar cache
        Cache::forget('system_settings');
        Cache::forget('public_system_settings');

        ActivityLog::logUpdate($systemSetting);

        return redirect()->route('system-settings.show', $systemSetting)
                        ->with('success', 'Configuração atualizada com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SystemSetting $systemSetting)
    {
        $this->authorize('delete', $systemSetting);

        ActivityLog::logDelete($systemSetting);
        
        $systemSetting->delete();

        // Limpar cache
        Cache::forget('system_settings');
        Cache::forget('public_system_settings');

        return redirect()->route('system-settings.index')
                        ->with('success', 'Configuração removida com sucesso!');
    }

    /**
     * Bulk update settings
     */
    public function bulkUpdate(Request $request)
    {
        $this->authorize('update', SystemSetting::class);

        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'required|string'
        ]);

        foreach ($settings['settings'] as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            
            if ($setting) {
                // Validar valor baseado no tipo
                $this->validateValueByType($value, $setting->type);
                
                $setting->update(['value' => $value]);
                
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'bulk_update',
                    'model_type' => SystemSetting::class,
                    'model_id' => $setting->id,
                    'description' => "Configuração {$key} atualizada em lote",
                    'old_values' => ['value' => $setting->getOriginal('value')],
                    'new_values' => ['value' => $value],
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);
            }
        }

        // Limpar cache
        Cache::forget('system_settings');
        Cache::forget('public_system_settings');

        return back()->with('success', 'Configurações atualizadas com sucesso!');
    }

    /**
     * Export settings
     */
    public function export(Request $request)
    {
        $this->authorize('viewAny', SystemSetting::class);

        $query = SystemSetting::query();

        if ($request->filled('group')) {
            $query->where('group', $request->group);
        }

        $settings = $query->orderBy('group')
                         ->orderBy('key')
                         ->get()
                         ->map(function ($setting) {
                             return [
                                 'key' => $setting->key,
                                 'value' => $setting->value,
                                 'type' => $setting->type,
                                 'description' => $setting->description,
                                 'group' => $setting->group,
                                 'is_public' => $setting->is_public
                             ];
                         });

        $filename = 'system_settings_' . now()->format('Y-m-d_H-i-s') . '.json';

        return response()->json($settings)
                        ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Import settings
     */
    public function import(Request $request)
    {
        $this->authorize('create', SystemSetting::class);

        $request->validate([
            'file' => 'required|file|mimes:json'
        ]);

        $content = file_get_contents($request->file('file')->path());
        $settings = json_decode($content, true);

        if (!is_array($settings)) {
            return back()->with('error', 'Arquivo JSON inválido.');
        }

        $imported = 0;
        $skipped = 0;

        foreach ($settings as $settingData) {
            if (!isset($settingData['key']) || !isset($settingData['value'])) {
                $skipped++;
                continue;
            }

            $existing = SystemSetting::where('key', $settingData['key'])->first();
            
            if ($existing) {
                $skipped++;
                continue;
            }

            try {
                $this->validateValueByType($settingData['value'], $settingData['type'] ?? 'string');
                
                SystemSetting::create([
                    'key' => $settingData['key'],
                    'value' => $settingData['value'],
                    'type' => $settingData['type'] ?? 'string',
                    'description' => $settingData['description'] ?? null,
                    'group' => $settingData['group'] ?? 'general',
                    'is_public' => $settingData['is_public'] ?? false
                ]);
                
                $imported++;
            } catch (\Exception $e) {
                $skipped++;
            }
        }

        // Limpar cache
        Cache::forget('system_settings');
        Cache::forget('public_system_settings');

        return back()->with('success', "Importação concluída: {$imported} configurações importadas, {$skipped} ignoradas.");
    }

    /**
     * Validate value by type
     */
    private function validateValueByType($value, $type)
    {
        switch ($type) {
            case 'integer':
                if (!is_numeric($value) || (int)$value != $value) {
                    throw new \InvalidArgumentException('Valor deve ser um número inteiro.');
                }
                break;
            case 'boolean':
                if (!in_array(strtolower($value), ['true', 'false', '1', '0'])) {
                    throw new \InvalidArgumentException('Valor deve ser true/false ou 1/0.');
                }
                break;
            case 'decimal':
                if (!is_numeric($value)) {
                    throw new \InvalidArgumentException('Valor deve ser um número decimal.');
                }
                break;
            case 'json':
                json_decode($value);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \InvalidArgumentException('Valor deve ser um JSON válido.');
                }
                break;
        }
    }
}