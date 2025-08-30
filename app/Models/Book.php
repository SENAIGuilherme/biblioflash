<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    protected $fillable = [
        'titulo',
        'autor',
        'editora',
        'isbn',
        'rfid_tag',
        'category_id',
        'descricao',
        'foto',
        'paginas',
        'ano_publicacao',
        'idioma',
        'preco',
        'quantidade_total',
        'quantidade_disponivel',
        'localizacao',
        'status',
        'total_emprestimos',
        'avaliacao_media'
    ];

    protected $casts = [
        'preco' => 'decimal:2',
        'avaliacao_media' => 'decimal:1',
        'ano_publicacao' => 'integer',
        'paginas' => 'integer',
        'quantidade_total' => 'integer',
        'quantidade_disponivel' => 'integer',
        'total_emprestimos' => 'integer'
    ];

    /**
     * Relacionamento com categoria
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relacionamento com reservas
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Relacionamento com empréstimos
     */
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    /**
     * Relacionamento com avaliações
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    /**
     * Relacionamento com favoritos
     */
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Usuários que favoritaram este livro
     */
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * Scope para livros disponíveis
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'disponivel')
                    ->where('quantidade_disponivel', '>', 0);
    }

    /**
     * Scope para busca por texto
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('titulo', 'like', "%{$search}%")
              ->orWhere('autor', 'like', "%{$search}%")
              ->orWhere('isbn', 'like', "%{$search}%")
              ->orWhere('descricao', 'like', "%{$search}%");
        });
    }

    /**
     * Verificar se está disponível para empréstimo
     */
    public function isAvailable(): bool
    {
        return $this->status === 'disponivel' && $this->quantidade_disponivel > 0;
    }

    /**
     * Decrementar quantidade disponível
     */
    public function decrementAvailable()
    {
        if ($this->quantidade_disponivel > 0) {
            $this->decrement('quantidade_disponivel');
        }
    }

    /**
     * Incrementar quantidade disponível
     */
    public function incrementAvailable()
    {
        if ($this->quantidade_disponivel < $this->quantidade_total) {
            $this->increment('quantidade_disponivel');
        }
    }

    /**
     * Atualizar avaliação média
     */
    public function updateAverageRating()
    {
        $average = $this->reviews()->where('aprovado', true)->avg('avaliacao');
        $this->update(['avaliacao_media' => $average ?: 0]);
    }
}
