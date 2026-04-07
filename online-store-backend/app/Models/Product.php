<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, Filterable;

    protected static function booted(): void
    {
        static::saving(function (Product $product) {
            if (blank($product->slug) && filled($product->name)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->getKey());
            }
        });
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'compare_price',
        'quantity',
        'sku',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'quantity' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // ---- Scopes ----

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    // ---- Accessors ----

    /**
     * هل المنتج عليه خصم؟
     */
    public function getHasDiscountAttribute(): bool
    {
        return $this->compare_price !== null && $this->compare_price > $this->price;
    }

    /**
     * نسبة الخصم
     */
    public function getDiscountPercentageAttribute(): ?int
    {
        if (!$this->has_discount) {
            return null;
        }

        return (int) round((($this->compare_price - $this->price) / $this->compare_price) * 100);
    }

    protected static function generateUniqueSlug(string $name, mixed $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);

        if (blank($baseSlug)) {
            $baseSlug = 'product';
        }

        $slug = $baseSlug;
        $counter = 2;

        while (static::query()
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->where('slug', $slug)
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
