<?php
/**
 * validation class for show category request
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class ShowCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $category = $this->route('category');

        return $category && $this->user()->id == $category->user_id;
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
