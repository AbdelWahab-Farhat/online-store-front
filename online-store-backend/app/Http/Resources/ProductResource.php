<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'compare_price' => $this->compare_price,
            'has_discount' => $this->has_discount,
            'discount_percentage' => $this->discount_percentage,
            'quantity' => $this->quantity,
            'in_stock' => $this->quantity > 0,
            'sku' => $this->sku,
            'is_active' => $this->is_active,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
