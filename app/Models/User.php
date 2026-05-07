<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
        ];
    }

    // =================== RELACIONES ===================
    
    public function stockEntries()
    {
        return $this->hasMany(StockEntry::class);
    }

    public function stockExits()
    {
        return $this->hasMany(StockExit::class);
    }

    public function stockAdjustments()
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function stockEntryBatches()
    {
        return $this->hasMany(StockEntryBatch::class);
    }

    public function stockExitBatches()
    {
        return $this->hasMany(StockExitBatch::class);
    }

    // =================== SCOPES ===================
    
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    public function scopeOperators($query)
    {
        return $query->where('role', 'operator');
    }

    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%");
        });
    }

    // =================== ACCESSORS ===================
    
    public function getIsAdminAttribute()
    {
        return $this->role === 'admin';
    }

    public function getIsOperatorAttribute()
    {
        return $this->role === 'operator';
    }

    public function getIsActiveAttribute()
    {
        return $this->status === 'active';
    }

    public function getRoleLabelAttribute()
    {
        return match($this->role) {
            'admin' => 'Administrador',
            'operator' => 'Operador',
            default => 'Desconocido',
        };
    }

    public function getRoleColorAttribute()
    {
        return match($this->role) {
            'admin' => 'danger',
            'operator' => 'primary',
            default => 'secondary',
        };
    }

    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'Activo' : 'Inactivo';
    }

    public function getStatusColorAttribute()
    {
        return $this->status === 'active' ? 'success' : 'secondary';
    }

    public function getInitialsAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
    }

    // =================== MÉTODOS AUXILIARES ===================
    
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isOperator()
    {
        return $this->role === 'operator';
    }

    public function canDelete()
    {
        return $this->stockMovements()->count() === 0;
    }

    public function getTotalMovementsCount()
    {
        return $this->stockMovements()->count();
    }

    public function getActivitySummary()
    {
        return [
            'entries' => $this->stockEntries()->count(),
            'exits' => $this->stockExits()->count(),
            'adjustments' => $this->stockAdjustments()->count(),
            'total_movements' => $this->stockMovements()->count(),
        ];
    }
}