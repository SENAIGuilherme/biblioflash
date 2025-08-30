<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'telefone',
        'email',
        'horario_funcionamento',
        'latitude',
        'longitude',
        'ativo'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'ativo' => 'boolean',
        'horario_funcionamento' => 'array'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeInCity($query, string $cidade)
    {
        return $query->where('cidade', 'like', "%{$cidade}%");
    }

    public function scopeInState($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    // Métodos
    public function getFullAddressAttribute(): string
    {
        return "{$this->endereco}, {$this->cidade} - {$this->estado}, {$this->cep}";
    }

    public function getFormattedPhoneAttribute(): string
    {
        $phone = preg_replace('/\D/', '', $this->telefone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $this->telefone;
    }

    public function calculateDistance(float $lat, float $lng): float
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        $earthRadius = 6371; // Raio da Terra em km

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($lat);
        $lonTo = deg2rad($lng);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function isOpen(): bool
    {
        if (!$this->horario_funcionamento) {
            return false;
        }

        $today = strtolower(now()->format('l')); // Nome do dia em inglês
        $currentTime = now()->format('H:i');

        $schedule = $this->horario_funcionamento[$today] ?? null;

        if (!$schedule || $schedule === 'fechado') {
            return false;
        }

        [$open, $close] = explode('-', $schedule);
        
        return $currentTime >= $open && $currentTime <= $close;
    }
}
