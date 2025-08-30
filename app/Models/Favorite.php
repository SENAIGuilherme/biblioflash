<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'book_id'
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

    // Scopes
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForBook($query, int $bookId)
    {
        return $query->where('book_id', $bookId);
    }

    // Métodos estáticos
    public static function toggle(int $userId, int $bookId): bool
    {
        $favorite = static::where('user_id', $userId)
                         ->where('book_id', $bookId)
                         ->first();

        if ($favorite) {
            $favorite->delete();
            return false; // Removido dos favoritos
        } else {
            static::create([
                'user_id' => $userId,
                'book_id' => $bookId
            ]);
            return true; // Adicionado aos favoritos
        }
    }

    public static function isFavorite(int $userId, int $bookId): bool
    {
        return static::where('user_id', $userId)
                    ->where('book_id', $bookId)
                    ->exists();
    }
}
