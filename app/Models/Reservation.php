<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Reservation extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'data_reserva',
        'data_expiracao',
        'status',
        'observacoes',
        'data_cancelamento',
        'motivo_cancelamento'
    ];

    protected $casts = [
        'data_reserva' => 'datetime',
        'data_expiracao' => 'datetime',
        'data_cancelamento' => 'datetime'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com livro
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Relacionamento com empréstimo
     */
    public function loan(): HasOne
    {
        return $this->hasOne(Loan::class);
    }

    /**
     * Scope para reservas ativas
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ativa');
    }

    /**
     * Scope para reservas expiradas
     */
    public function scopeExpired($query)
    {
        return $query->where('data_expiracao', '<', now())
                    ->where('status', 'ativa');
    }

    /**
     * Verificar se a reserva está expirada
     */
    public function isExpired(): bool
    {
        return $this->data_expiracao && $this->data_expiracao->isPast() && $this->status === 'ativa';
    }

    /**
     * Cancelar reserva
     */
    public function cancel(string $motivo = null)
    {
        $this->update([
            'status' => 'cancelada',
            'data_cancelamento' => now(),
            'motivo_cancelamento' => $motivo
        ]);
    }

    /**
     * Marcar como retirada (convertida em empréstimo)
     */
    public function markAsWithdrawn()
    {
        $this->update(['status' => 'retirada']);
    }

    /**
     * Expirar reserva automaticamente
     */
    public function expire()
    {
        if ($this->isExpired()) {
            $this->update(['status' => 'expirada']);
        }
    }

    /**
     * Boot method para definir data de expiração automaticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($reservation) {
            if (!$reservation->data_expiracao) {
                // Definir expiração em 48 horas (configurável)
                $hours = config('library.reservation_expiry_hours', 48);
                $reservation->data_expiracao = now()->addHours($hours);
            }
        });
    }
}
