<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'nome',
        'slug',
        'descricao',
        'cor',
        'ativo'
    ];

    protected $casts = [
        'ativo' => 'boolean'
    ];

    /**
     * Relacionamento com livros
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Scope para categorias ativas
     */
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Gerar slug automaticamente
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->nome);
            }
        });
        
        static::updating(function ($category) {
            if ($category->isDirty('nome') && empty($category->slug)) {
                $category->slug = Str::slug($category->nome);
            }
        });
    }

    /**
     * Contar livros da categoria
     */
    public function getBooksCountAttribute()
    {
        return $this->books()->count();
    }
}
