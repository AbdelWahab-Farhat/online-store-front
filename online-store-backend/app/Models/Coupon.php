<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'min_order_amount',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_type' => DiscountType::class,
            'discount_value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'usage_limit' => 'integer',
            'used_count' => 'integer',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    // ---- Relationships ----

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ---- Scopes ----

    public function scopeValid($query)
    {
        return $query
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            });
    }

    // ---- Methods ----

    /**
     * حساب قيمة الخصم على مبلغ معين
     */
    public function calculateDiscount(float $amount): float
    {
        $discount = match ($this->discount_type) {
            DiscountType::Percentage => $amount * ($this->discount_value / 100),
            DiscountType::Fixed => $this->discount_value,
        };

        // تطبيق الحد الأقصى للخصم
        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        // الخصم ما يتجاوز المبلغ الأصلي
        return min($discount, $amount);
    }

    /**
     * هل الكوبون صالح للاستخدام؟
     */
    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->starts_at && $this->starts_at->isFuture()) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->usage_limit && $this->used_count >= $this->usage_limit) return false;

        return true;
    }
}
