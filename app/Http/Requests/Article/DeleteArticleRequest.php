<?php
/**
 * validation class for delete article request
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class DeleteArticleRequest extends FormRequest
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
            //
        ];
    }
}
