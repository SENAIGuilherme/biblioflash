<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'loan_id',
        'valor',
        'descricao',
        'tipo',
        'data_geracao',
        'data_vencimento',
        'data_pagamento',
        'status',
        'forma_pagamento',
        'observacoes',
        'funcionario_id'
    ];

    protected $casts = [
        'valor' => 'decimal:2',
        'data_geracao' => 'datetime',
        'data_vencimento' => 'datetime',
        'data_pagamento' => 'datetime'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_id');
    }

    // Scopes
    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopePaga($query)
    {
        return $query->where('status', 'paga');
    }

    public function scopeVencida($query)
    {
        return $query->where('status', 'vencida');
    }

    public function scopeOverdue($query)
    {
        return $query->where('data_vencimento', '<', now())
                    ->where('status', 'pendente');
    }

    // Métodos
    public function isOverdue(): bool
    {
        return $this->data_vencimento < now() && $this->status === 'pendente';
    }

    public function markAsPaid(string $formaPagamento = null, int $funcionarioId = null): bool
    {
        return $this->update([
            'status' => 'paga',
            'data_pagamento' => now(),
            'forma_pagamento' => $formaPagamento,
            'funcionario_id' => $funcionarioId
        ]);
    }

    public function cancel(string $observacao = null): bool
    {
        return $this->update([
            'status' => 'cancelada',
            'observacoes' => $observacao
        ]);
    }

    public function markAsOverdue(): bool
    {
        if ($this->isOverdue()) {
            return $this->update(['status' => 'vencida']);
        }
        return false;
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($fine) {
            if (!$fine->data_geracao) {
                $fine->data_geracao = now();
            }
            if (!$fine->data_vencimento) {
                $fine->data_vencimento = now()->addDays(30); // 30 dias para pagamento
            }
            if (!$fine->status) {
                $fine->status = 'pendente';
            }
        });
    }
}
