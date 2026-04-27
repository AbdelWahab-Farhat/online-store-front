<?php

namespace App\Http\Requests;

use App\Models\Announcement;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnnouncementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:255'],
            'link' => ['nullable', 'string', 'max:500'],
            'link_text' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * التحقق الإضافي بعد التحقق من القواعد
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (Announcement::count() >= Announcement::MAX_ANNOUNCEMENTS) {
                $validator->errors()->add(
                    'limit',
                    'تم الوصول للحد الأقصى من اللوحات الإعلانية (' . Announcement::MAX_ANNOUNCEMENTS . ').'
                );
            }
        });
    }
}
