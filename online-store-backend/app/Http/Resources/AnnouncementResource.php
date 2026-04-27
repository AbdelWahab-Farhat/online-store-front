<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_active' => $this->is_active,
            'sort_order' => $this->sort_order,
            'link' => $this->link,
            'link_text' => $this->link_text,
            'image' => new ImageResource($this->whenLoaded('image')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
