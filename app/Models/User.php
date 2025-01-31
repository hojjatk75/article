<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'family',
        'email',
        'password',
        'verify_code',
        'verify_code_expire_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verify_code',
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
            'verify_code' => 'hashed',
            'verify_code_expire_at' => 'datetime',
        ];
    }

    /**
     * @return bool
     */
    public function verifyCodeIsExpired(): bool
    {
        return now()->diffInSeconds($this->verify_code_expire_at) <= 0;
    }

    /**
     * @return HasMany
     */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class, 'user_id', 'id');
    }

    /**
     * @param $query
     * @return void
     */
    public function scopeVerifiedEmail($query): void
    {
        $query->whereNotNull('email_verified_at');
    }

    /**
     * @return bool
     */
    public function completedRegister(): bool
    {
        return (!empty($this->name) && !empty($this->family));
    }
}
