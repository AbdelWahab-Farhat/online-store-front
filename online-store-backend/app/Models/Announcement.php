<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Announcement extends Model
{
    use HasFactory;

    /**
     * الحد الأقصى للوحات الإعلانية
     */
    public const MAX_ANNOUNCEMENTS = 6;

    protected $fillable = [
        'title',
        'description',
        'is_active',
        'sort_order',
        'link',
        'link_text',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ---- Relationships ----

    /**
     * صورة اللوحة الإعلانية (واحدة فقط عبر polymorphic)
     */
    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    // ---- Scopes ----

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at', 'desc');
    }
}
