<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Models;

use App\Enums\ArticleStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'content', 'status', 'published_at', 'category_id'];
    protected $casts = [
        'published_at' => 'datetime',
    ];

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
    public function scopePublished($query): void
    {
        $query->where('status', ArticleStatus::PUBLISHED->value);
    }

    /**
     * @return HasOne
     */
    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
