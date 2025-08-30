<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Loan extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'reservation_id',
        'data_emprestimo',
        'data_prevista_devolucao',
        'data_devolucao',
        'status',
        'observacoes_emprestimo',
        'observacoes_devolucao',
        'funcionario_emprestimo_id',
        'funcionario_devolucao_id',
        'renovado',
        'numero_renovacoes',
        'multa_valor',
        'multa_paga'
    ];

    protected $casts = [
        'data_emprestimo' => 'datetime',
        'data_prevista_devolucao' => 'datetime',
        'data_devolucao' => 'datetime',
        'renovado' => 'boolean',
        'numero_renovacoes' => 'integer',
        'multa_valor' => 'decimal:2',
        'multa_paga' => 'boolean'
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
     * Relacionamento com reserva
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * Funcionário que fez o empréstimo
     */
    public function funcionarioEmprestimo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_emprestimo_id');
    }

    /**
     * Funcionário que fez a devolução
     */
    public function funcionarioDevolucao(): BelongsTo
    {
        return $this->belongsTo(User::class, 'funcionario_devolucao_id');
    }

    /**
     * Relacionamento com multas
     */
    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    /**
     * Scope para empréstimos ativos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'ativo');
    }

    /**
     * Scope para empréstimos em atraso
     */
    public function scopeOverdue($query)
    {
        return $query->where('data_prevista_devolucao', '<', now())
                    ->where('status', 'ativo');
    }

    /**
     * Verificar se está em atraso
     */
    public function isOverdue(): bool
    {
        return $this->data_prevista_devolucao->isPast() && $this->status === 'ativo';
    }

    /**
     * Calcular dias de atraso
     */
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }
        
        return now()->diffInDays($this->data_prevista_devolucao);
    }

    /**
     * Calcular multa por atraso
     */
    public function calculateFine(): float
    {
        $daysOverdue = $this->getDaysOverdue();
        if ($daysOverdue <= 0) {
            return 0;
        }
        
        $finePerDay = config('library.fine_per_day', 2.00);
        return $daysOverdue * $finePerDay;
    }

    /**
     * Renovar empréstimo
     */
    public function renew(int $days = null): bool
    {
        $maxRenewals = config('library.max_renewals', 2);
        
        if ($this->numero_renovacoes >= $maxRenewals) {
            return false;
        }
        
        $days = $days ?: config('library.loan_duration_days', 14);
        
        $this->update([
            'data_prevista_devolucao' => $this->data_prevista_devolucao->addDays($days),
            'renovado' => true,
            'numero_renovacoes' => $this->numero_renovacoes + 1
        ]);
        
        return true;
    }

    /**
     * Devolver livro
     */
    public function return(int $funcionarioId = null, string $observacoes = null)
    {
        $this->update([
            'data_devolucao' => now(),
            'status' => 'devolvido',
            'funcionario_devolucao_id' => $funcionarioId,
            'observacoes_devolucao' => $observacoes
        ]);
        
        // Incrementar quantidade disponível do livro
        $this->book->incrementAvailable();
        
        // Gerar multa se houver atraso
        if ($this->isOverdue()) {
            $this->generateFine();
        }
    }

    /**
     * Gerar multa por atraso
     */
    protected function generateFine()
    {
        $fineAmount = $this->calculateFine();
        
        if ($fineAmount > 0) {
            Fine::create([
                'user_id' => $this->user_id,
                'loan_id' => $this->id,
                'valor' => $fineAmount,
                'descricao' => 'Multa por atraso na devolução',
                'tipo' => 'atraso',
                'data_geracao' => now(),
                'data_vencimento' => now()->addDays(30)
            ]);
        }
    }

    /**
     * Boot method para definir data de devolução automaticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($loan) {
            if (!$loan->data_prevista_devolucao) {
                $days = config('library.loan_duration_days', 14);
                $loan->data_prevista_devolucao = $loan->data_emprestimo->addDays($days);
            }
        });
    }
}
