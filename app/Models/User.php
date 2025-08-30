<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'telefone',
        'endereco',
        'cidade',
        'estado',
        'cep',
        'data_nascimento',
        'tipo',
        'ativo',
        'ultimo_acesso'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'data_nascimento' => 'date',
            'ativo' => 'boolean',
            'ultimo_acesso' => 'datetime'
        ];
    }

    // Relacionamentos
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class);
    }

    public function fines(): HasMany
    {
        return $this->hasMany(Fine::class);
    }

    public function bookReviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(BookReview::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Relacionamentos como funcionário
    public function processedFines(): HasMany
    {
        return $this->hasMany(Fine::class, 'funcionario_id');
    }

    public function moderatedReviews(): HasMany
    {
        return $this->hasMany(BookReview::class, 'moderador_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeClients($query)
    {
        return $query->where('tipo', 'cliente');
    }

    public function scopeAdmins($query)
    {
        return $query->where('tipo', 'admin');
    }

    public function scopeLibrarians($query)
    {
        return $query->where('tipo', 'bibliotecario');
    }

    // Métodos
    public function isAdmin(): bool
    {
        return $this->tipo === 'admin';
    }

    public function isLibrarian(): bool
    {
        return $this->tipo === 'bibliotecario';
    }

    public function isClient(): bool
    {
        return $this->tipo === 'cliente';
    }

    public function canManageBooks(): bool
    {
        return $this->isAdmin() || $this->isLibrarian();
    }

    public function getActiveLoansCount(): int
    {
        return $this->loans()->active()->count();
    }

    public function getPendingFinesCount(): int
    {
        return $this->fines()->pendente()->count();
    }

    public function getPendingFinesTotal(): float
    {
        return $this->fines()->pendente()->sum('valor');
    }

    public function canBorrowBooks(): bool
    {
        if (!$this->ativo || !$this->isClient()) {
            return false;
        }

        $maxBooks = SystemSetting::getMaxBooksPerUser();
        $activeLoans = $this->getActiveLoansCount();
        $pendingFines = $this->getPendingFinesCount();

        return $activeLoans < $maxBooks && $pendingFines === 0;
    }

    public function getFormattedPhoneAttribute(): string
    {
        if (!$this->telefone) {
            return '';
        }

        $phone = preg_replace('/\D/', '', $this->telefone);
        
        if (strlen($phone) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $phone);
        } elseif (strlen($phone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $phone);
        }
        
        return $this->telefone;
    }

    public function getAgeAttribute(): ?int
    {
        if (!$this->data_nascimento) {
            return null;
        }

        return Carbon::parse($this->data_nascimento)->age;
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->endereco,
            $this->cidade,
            $this->estado,
            $this->cep
        ]);

        return implode(', ', $parts);
    }

    public function updateLastAccess(): void
    {
        $this->update(['ultimo_acesso' => now()]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (!$user->tipo) {
                $user->tipo = 'cliente';
            }
            if (!isset($user->ativo)) {
                $user->ativo = true;
            }
        });
    }
}
