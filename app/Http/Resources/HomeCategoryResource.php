<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
            'sub_categories' => $this->when($this->subCategories()->active()->whereHas('articles')->count(), self::collection($this->subCategories()->active()->whereHas('articles')->get())),
            'articles_count' => $this->articles()->published()->count()
        ];
    }
}
