<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'path',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    // ---- Relationships ----

    /**
     * الموديل المرتبط بالصورة (Product, Category, أو أي موديل آخر)
     */
    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    // ---- Accessors ----

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
