<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Models;

use App\Enums\CategoryStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'status', 'parent_id'];

    /**
     * @param $query
     * @return void
     */
    public function scopeUserId($query): void
    {
        $query->where('user_id', '=', Auth::id());
    }

    /**
     * @param $query
     * @return void
     */
    public function scopeActive($query): void
    {
        $query->where('status', CategoryStatus::ACTIVE->value);
    }

    /**
     * @return HasMany
     */
    public function subCategories(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * @return HasMany
     */
    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
