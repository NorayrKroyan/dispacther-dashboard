<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispatchDriverTrackingSnapshot;
use App\Support\DispatchEvents;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DispatchTrackingSnapshotController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_driver' => ['required', 'integer'],
            'id_join' => ['required', 'integer'],
            'last_event' => ['nullable', Rule::in(DispatchEvents::values())],
            'miles_to_job' => ['nullable', 'numeric'],
            'eta_to_deliver_minutes' => ['nullable', 'integer'],
            'gps_lat' => ['nullable', 'numeric'],
            'gps_lng' => ['nullable', 'numeric'],
            'gps_recorded_at' => ['nullable', 'date'],
            'source_table' => ['nullable', 'string', 'max:120'],
            'source_id' => ['nullable', 'string', 'max:120'],
        ]);

        $snapshot = DispatchDriverTrackingSnapshot::query()->updateOrCreate(
            [
                'id_driver' => $data['id_driver'],
                'id_join' => $data['id_join'],
            ],
            [
                'last_event' => $data['last_event'] ?? '',
                'miles_to_job' => $data['miles_to_job'] ?? null,
                'eta_to_deliver_minutes' => $data['eta_to_deliver_minutes'] ?? null,
                'gps_lat' => $data['gps_lat'] ?? null,
                'gps_lng' => $data['gps_lng'] ?? null,
                'gps_recorded_at' => $data['gps_recorded_at'] ?? null,
                'source_table' => $data['source_table'] ?? null,
                'source_id' => $data['source_id'] ?? null,
                'synced_at' => now(),
            ]
        );

        return response()->json([
            'message' => 'Tracking snapshot saved.',
            'snapshot' => $snapshot,
        ]);
    }
}
