<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AppSettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppStatusController extends Controller
{
    public function __construct(private readonly AppSettingsService $service) {}

    public function status(Request $request): JsonResponse
    {
        $version  = $request->query('version', '1.0.0');
        $platform = $request->query('platform', 'ios');

        $result = $this->service->checkAppStatus((string) $version, (string) $platform);

        return response()->json($result);
    }
}
