<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAnnouncementRequest;
use App\Http\Requests\UpdateAnnouncementRequest;
use App\Http\Resources\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;

class AnnouncementController extends Controller
{
    /**
     * عرض كل اللوحات الإعلانية
     * الزوار يرون النشطة فقط، الإدارة ترى الكل
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Announcement::with('image')->ordered();

        // فلترة اللوحات النشطة فقط للزوار
        if (! $request->user()?->hasManagementAccess()) {
            $query->active();
        }

        $announcements = $query->get();

        return AnnouncementResource::collection($announcements);
    }

    /**
     * عرض لوحة إعلانية واحدة
     */
    public function show(Announcement $announcement): AnnouncementResource
    {
        $announcement->load('image');

        return new AnnouncementResource($announcement);
    }

    /**
     * إنشاء لوحة إعلانية جديدة (أدمن/موظف)
     */
    public function store(StoreAnnouncementRequest $request): JsonResponse
    {
        $data = $request->validated();

        // حذف الصورة من بيانات الإنشاء
        $announcementData = collect($data)->except(['image'])->toArray();
        $announcement = Announcement::create($announcementData);

        // رفع صورة اللوحة الإعلانية (مطلوبة)
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('announcements', 'public');
            $announcement->image()->create(['path' => $path]);
        }

        $announcement->load('image');

        return response()->json([
            'message' => 'تم إنشاء اللوحة الإعلانية بنجاح.',
            'data' => new AnnouncementResource($announcement),
        ], 201);
    }

    /**
     * تعديل لوحة إعلانية (أدمن/موظف)
     */
    public function update(UpdateAnnouncementRequest $request, Announcement $announcement): JsonResponse
    {
        $data = $request->validated();

        $announcementData = collect($data)->except(['image'])->toArray();
        $announcement->update($announcementData);

        // استبدال الصورة (حذف القديمة + رفع الجديدة)
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة من التخزين
            if ($announcement->image) {
                Storage::disk('public')->delete($announcement->image->path);
                $announcement->image->delete();
            }
            $path = $request->file('image')->store('announcements', 'public');
            $announcement->image()->create(['path' => $path]);
        }

        $announcement->load('image');

        return response()->json([
            'message' => 'تم تعديل اللوحة الإعلانية بنجاح.',
            'data' => new AnnouncementResource($announcement),
        ]);
    }

    /**
     * حذف لوحة إعلانية (أدمن/موظف)
     */
    public function destroy(Announcement $announcement): JsonResponse
    {
        // حذف الصورة من التخزين
        if ($announcement->image) {
            Storage::disk('public')->delete($announcement->image->path);
            $announcement->image->delete();
        }

        $announcement->delete();

        return response()->json([
            'message' => 'تم حذف اللوحة الإعلانية بنجاح.',
        ]);
    }

    /**
     * تبديل حالة النشاط (أدمن/موظف)
     */
    public function toggleActive(Announcement $announcement): JsonResponse
    {
        $announcement->update(['is_active' => ! $announcement->is_active]);
        $announcement->load('image');

        return response()->json([
            'message' => $announcement->is_active
                ? 'تم تفعيل اللوحة الإعلانية.'
                : 'تم تعطيل اللوحة الإعلانية.',
            'data' => new AnnouncementResource($announcement),
        ]);
    }
}
