<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     * يسمح فقط للأدمن والموظفين
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->hasManagementAccess()) {
            return response()->json([
                'message' => 'غير مصرح لك بالوصول.',
            ], 403);
        }

        return $next($request);
    }
}
