<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DispatchDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispatchDashboardController extends Controller
{
    public function __construct(private readonly DispatchDashboardService $dashboardService)
    {
    }

    public function jobs(): JsonResponse
    {
        return response()->json([
            'jobs' => $this->dashboardService->jobs(),
        ]);
    }

    public function show(Request $request, int $idJoin): JsonResponse
    {
        return response()->json(
            $this->dashboardService->dashboard(
                $idJoin,
                $request->query('dispatcher_name')
            )
        );
    }
}
