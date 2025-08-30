<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    // Desabilitar updated_at pois só precisamos de created_at
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'description',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForModel($query, string $modelType, int $modelId = null)
    {
        $query = $query->where('model_type', $modelType);
        
        if ($modelId) {
            $query->where('model_id', $modelId);
        }
        
        return $query;
    }

    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Métodos estáticos para logging
    public static function logActivity(
        string $action,
        $model = null,
        array $oldValues = [],
        array $newValues = [],
        string $description = null,
        int $userId = null
    ): self {
        $userId = $userId ?? auth()->id();
        $request = request();

        return static::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'description' => $description,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null
        ]);
    }

    public static function logLogin(int $userId): self
    {
        return static::logActivity('login', null, [], [], 'Usuário fez login', $userId);
    }

    public static function logLogout(int $userId): self
    {
        return static::logActivity('logout', null, [], [], 'Usuário fez logout', $userId);
    }

    public static function logCreate($model, int $userId = null): self
    {
        return static::logActivity(
            'create',
            $model,
            [],
            $model->getAttributes(),
            'Registro criado',
            $userId
        );
    }

    public static function logUpdate($model, array $oldValues, int $userId = null): self
    {
        return static::logActivity(
            'update',
            $model,
            $oldValues,
            $model->getChanges(),
            'Registro atualizado',
            $userId
        );
    }

    public static function logDelete($model, int $userId = null): self
    {
        return static::logActivity(
            'delete',
            $model,
            $model->getAttributes(),
            [],
            'Registro excluído',
            $userId
        );
    }

    // Métodos de formatação
    public function getFormattedChangesAttribute(): array
    {
        $changes = [];
        
        if ($this->old_values && $this->new_values) {
            foreach ($this->new_values as $key => $newValue) {
                $oldValue = $this->old_values[$key] ?? null;
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue
                    ];
                }
            }
        }
        
        return $changes;
    }

    public function getActionLabelAttribute(): string
    {
        $labels = [
            'create' => 'Criado',
            'update' => 'Atualizado',
            'delete' => 'Excluído',
            'login' => 'Login',
            'logout' => 'Logout',
            'view' => 'Visualizado',
            'download' => 'Download',
            'export' => 'Exportado'
        ];

        return $labels[$this->action] ?? ucfirst($this->action);
    }
}
