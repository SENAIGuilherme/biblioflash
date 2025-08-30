<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookReview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id',
        'loan_id',
        'avaliacao',
        'comentario',
        'recomenda',
        'aprovado',
        'data_aprovacao',
        'moderador_id'
    ];

    protected $casts = [
        'avaliacao' => 'integer',
        'recomenda' => 'boolean',
        'aprovado' => 'boolean',
        'data_aprovacao' => 'datetime'
    ];

    // Relacionamentos
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    public function moderador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderador_id');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('aprovado', true);
    }

    public function scopePending($query)
    {
        return $query->where('aprovado', false);
    }

    public function scopeRecommended($query)
    {
        return $query->where('recomenda', true);
    }

    public function scopeByRating($query, int $rating)
    {
        return $query->where('avaliacao', $rating);
    }

    // Métodos
    public function approve(int $moderadorId): bool
    {
        return $this->update([
            'aprovado' => true,
            'data_aprovacao' => now(),
            'moderador_id' => $moderadorId
        ]);
    }

    public function reject(): bool
    {
        return $this->delete();
    }

    public function getStarsAttribute(): string
    {
        return str_repeat('★', $this->avaliacao) . str_repeat('☆', 5 - $this->avaliacao);
    }

    // Validação personalizada
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($review) {
            // Validar se a avaliação está entre 1 e 5
            if ($review->avaliacao < 1 || $review->avaliacao > 5) {
                throw new \InvalidArgumentException('A avaliação deve estar entre 1 e 5.');
            }
        });

        static::updating(function ($review) {
            // Validar se a avaliação está entre 1 e 5
            if ($review->avaliacao < 1 || $review->avaliacao > 5) {
                throw new \InvalidArgumentException('A avaliação deve estar entre 1 e 5.');
            }
        });
    }
}
