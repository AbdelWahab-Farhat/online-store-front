<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';
    case Paid = 'paid';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'قيد الانتظار',
            self::Confirmed => 'تمت الموافقة',
            self::Processing => 'قيد التجهيز',
            self::Delivered => 'تم التوصيل',
            self::Cancelled => 'ملغي',
            self::Paid => 'تم الدفع',
        };
    }
}
