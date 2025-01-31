<?php
/**
 * validation class for update article request
 * @author Hojjat koochak zadeh
 */
namespace App\Http\Requests\Article;

use App\Enums\ArticleStatus;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $article = $this->route('article');

        return $article && $this->user()->id == $article->user_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3',
            'content' => 'required|string|min:10',
            'published_at' => ['required_if:status,'.ArticleStatus::SCHEDULED->value,
                function ($attribute, $value, $fail) {
                    if ($this->status == ArticleStatus::SCHEDULED->value && Carbon::parse($value)->lessThanOrEqualTo(now())) {
                        $fail(__(':attribute must be greater than now'));
                    }
                }
            ],
            'category_id' => ['nullable', Rule::exists('categories', 'id')->where('user_id', Auth::id())],
            'status' => [Rule::enum(ArticleStatus::class)],
        ];
    }
}
