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
        // حذف جدول صور المنتجات القديم
        Schema::dropIfExists('product_images');

        // حذف عمود الصورة من التصنيفات
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('image');
        });

        // إنشاء جدول الصور الموحد (Polymorphic)
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->morphs('imageable'); // imageable_id + imageable_type
            $table->string('path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');

        Schema::table('categories', function (Blueprint $table) {
            $table->string('image')->nullable()->after('slug');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('image_path');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }
};
