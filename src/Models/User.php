<?php

declare(strict_types=1);

namespace Gomu\Auth\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable implements FilamentUser, HasName
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;
    protected $table = 'users';

    public function getTable()
    {
        $schema = config('gomu-auth.schema');
        return $schema ? $schema . '.' . $this->table : $this->table;
    }

    protected static function newFactory()
    {
        return \Database\Factories\UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'username',
        'email',
        'password_hash',
        'user_type',
        'role_id',
        'last_login',
        'force_password_change',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'last_login' => 'datetime',
            'force_password_change' => 'boolean',
        ];
    }

    public $incrementing = false;
    protected $keyType = 'string';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    // Allow writing to a virtual `password` attribute which stores into `password_hash`.
    protected function password(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value ? ['password_hash' => bcrypt($value)] : []
        );
    }

    /**
     * Display name used by Filament.
     */
    public function getFilamentName(): string
    {
        return $this->username ?? $this->email ?? 'User';
    }

    /**
     * Generate NIP: YYMMDD + 3-digit unique counter
     */
    public static function generateNip(): string
    {
        $today = now()->format('ymd');
        $counter = 1;

        // Find the next available NIP for today via employees table
        while (Employee::where('nip', $today . str_pad((string)$counter, 3, '0', STR_PAD_LEFT))->exists()) {
            $counter++;
        }

        return $today . str_pad((string)$counter, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Validate password against hashed password_hash
     */
    public function validatePassword(string $password): bool
    {
        return \Illuminate\Support\Facades\Hash::check($password, $this->password_hash);
    }

    /**
     * Allow access to all Filament panels (adjust if needed).
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    // Relationships
    // public function role()
    // {
    //     return $this->belongsTo(Role::class);
    // }

    // Link back to the employee profile (one-to-one)
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // Scopes
    public function scopeInternal($query)
    {
        return $query->where('user_type', 'internal');
    }

    public function scopeExternal($query)
    {
        return $query->where('user_type', 'external');
    }

    // Check if user is internal
    public function isInternal(): bool
    {
        return $this->user_type === 'internal';
    }

    // Check if user is external
    public function isExternal(): bool
    {
        return $this->user_type === 'external';
    }
}