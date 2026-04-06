<?php

namespace App\Http\Middleware;

use App\Models\AppSettings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        $settings = AppSettings::get();

        if ($settings->maintenance_mode) {
            return response()->json([
                'status'      => 'maintenance',
                'maintenance' => [
                    'enabled' => true,
                    'message' => $settings->maintenance_message
                        ?? 'Uygulama şu anda bakımda, kısa süre içinde geri döneceğiz.',
                ],
            ], 503);
        }

        return $next($request);
    }
}
