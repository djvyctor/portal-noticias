<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NewsResource extends JsonResource
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
            'slug' => $this->slug,
            'content' => $this->content,
            'image_path' => $this->image_path,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'author_name' => $this->author_name,
            'rejection_reason' => $this->rejection_reason,
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'published_at' => $this->published_at?->toISOString(),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'user' => $this->whenLoaded('user', function () {
                if (!$this->user) {
                    return null;
                }
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email ?? null,
                ];
            }),
            'category' => $this->whenLoaded('category', function () {
                if (!$this->category) {
                    return null;
                }
                return [
                    'id' => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),
        ];
    }
}
