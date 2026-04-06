<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('discount_type'); // 'percentage' or 'fixed'
            $table->decimal('discount_value', 10, 2); // القيمة (نسبة مئوية أو مبلغ ثابت)
            $table->decimal('min_order_amount', 10, 2)->nullable(); // الحد الأدنى للطلب
            $table->decimal('max_discount', 10, 2)->nullable(); // أقصى خصم (للنسبة المئوية)
            $table->unsignedInteger('usage_limit')->nullable(); // الحد الأقصى للاستخدام
            $table->unsignedInteger('used_count')->default(0); // عدد مرات الاستخدام
            $table->dateTime('starts_at')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
