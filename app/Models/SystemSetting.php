<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
        'group',
        'is_public'
    ];

    protected $casts = [
        'is_public' => 'boolean'
    ];

    // Scopes
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public function scopeByKey($query, string $key)
    {
        return $query->where('key', $key);
    }

    // Métodos estáticos para gerenciar configurações
    public static function get(string $key, $default = null)
    {
        $cacheKey = "system_setting_{$key}";
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return static::castValue($setting->value, $setting->type);
        });
    }

    public static function set(string $key, $value, string $type = 'string', string $description = null, string $group = 'general', bool $isPublic = false): self
    {
        $setting = static::updateOrCreate(
            ['key' => $key],
            [
                'value' => static::prepareValue($value, $type),
                'type' => $type,
                'description' => $description,
                'group' => $group,
                'is_public' => $isPublic
            ]
        );

        // Limpar cache
        Cache::forget("system_setting_{$key}");
        
        return $setting;
    }

    public static function forget(string $key): bool
    {
        $deleted = static::where('key', $key)->delete();
        
        if ($deleted) {
            Cache::forget("system_setting_{$key}");
        }
        
        return $deleted > 0;
    }

    public static function getByGroup(string $group): array
    {
        $cacheKey = "system_settings_group_{$group}";
        
        return Cache::remember($cacheKey, 3600, function () use ($group) {
            $settings = static::where('group', $group)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    public static function getPublicSettings(): array
    {
        $cacheKey = 'system_settings_public';
        
        return Cache::remember($cacheKey, 3600, function () {
            $settings = static::where('is_public', true)->get();
            $result = [];
            
            foreach ($settings as $setting) {
                $result[$setting->key] = static::castValue($setting->value, $setting->type);
            }
            
            return $result;
        });
    }

    // Métodos auxiliares
    protected static function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'integer':
                return (int) $value;
            case 'float':
            case 'decimal':
                return (float) $value;
            case 'array':
            case 'json':
                return json_decode($value, true);
            case 'string':
            default:
                return (string) $value;
        }
    }

    protected static function prepareValue($value, string $type): string
    {
        switch ($type) {
            case 'boolean':
                return $value ? '1' : '0';
            case 'array':
            case 'json':
                return json_encode($value);
            default:
                return (string) $value;
        }
    }

    // Accessor para valor tipado
    public function getTypedValueAttribute()
    {
        return static::castValue($this->value, $this->type);
    }

    // Limpar cache quando o modelo for atualizado
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            Cache::forget("system_settings_group_{$setting->group}");
            Cache::forget('system_settings_public');
        });

        static::deleted(function ($setting) {
            Cache::forget("system_setting_{$setting->key}");
            Cache::forget("system_settings_group_{$setting->group}");
            Cache::forget('system_settings_public');
        });
    }

    // Métodos de conveniência para configurações específicas
    public static function getLoanDuration(): int
    {
        return static::get('loan_duration_days', 14);
    }

    public static function getMaxRenewals(): int
    {
        return static::get('max_renewals', 2);
    }

    public static function getFinePerDay(): float
    {
        return static::get('fine_per_day', 1.00);
    }

    public static function getReservationExpiryHours(): int
    {
        return static::get('reservation_expiry_hours', 48);
    }

    public static function getMaxBooksPerUser(): int
    {
        return static::get('max_books_per_user', 3);
    }
}
