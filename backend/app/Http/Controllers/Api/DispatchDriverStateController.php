<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispatchDriverCurrentState;
use App\Models\DispatchDriverStateHistory;
use App\Support\DispatchEvents;
use App\Support\DispatchStatuses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DispatchDriverStateController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_driver' => ['required', 'integer'],
            'id_join' => ['required', 'integer'],
            'current_status' => ['required', Rule::in(DispatchStatuses::values())],
            'state_started_at' => ['nullable', 'date'],
            'predicted_return_at' => ['nullable', 'date'],
            'last_event' => ['nullable', Rule::in(DispatchEvents::values())],
            'updated_by_user_id' => ['nullable', 'integer'],
            'updated_by_name' => ['nullable', 'string', 'max:120'],
            'history_note' => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($data) {
            $current = DispatchDriverCurrentState::query()
                ->where('id_driver', $data['id_driver'])
                ->where('id_join', $data['id_join'])
                ->first();

            $startedAt = $data['state_started_at'] ?? now();

            if (!$current) {
                DispatchDriverCurrentState::query()->create([
                    'id_driver' => $data['id_driver'],
                    'id_join' => $data['id_join'],
                    'current_status' => $data['current_status'],
                    'state_started_at' => $startedAt,
                    'predicted_return_at' => $data['predicted_return_at'] ?? null,
                    'last_event' => $data['last_event'] ?? '',
                    'updated_by_user_id' => $data['updated_by_user_id'] ?? null,
                    'updated_by_name' => $data['updated_by_name'] ?? null,
                ]);

                DispatchDriverStateHistory::query()->create([
                    'id_driver' => $data['id_driver'],
                    'id_join' => $data['id_join'],
                    'status' => $data['current_status'],
                    'started_at' => $startedAt,
                    'predicted_return_at' => $data['predicted_return_at'] ?? null,
                    'last_event' => $data['last_event'] ?? '',
                    'changed_by_user_id' => $data['updated_by_user_id'] ?? null,
                    'changed_by_name' => $data['updated_by_name'] ?? null,
                    'note' => $data['history_note'] ?? null,
                ]);

                return;
            }

            $statusChanged = $current->current_status !== $data['current_status'];

            if ($statusChanged) {
                DispatchDriverStateHistory::query()
                    ->where('id_driver', $data['id_driver'])
                    ->where('id_join', $data['id_join'])
                    ->whereNull('ended_at')
                    ->update([
                        'ended_at' => now(),
                    ]);

                DispatchDriverStateHistory::query()->create([
                    'id_driver' => $data['id_driver'],
                    'id_join' => $data['id_join'],
                    'status' => $data['current_status'],
                    'started_at' => $startedAt,
                    'predicted_return_at' => $data['predicted_return_at'] ?? null,
                    'last_event' => $data['last_event'] ?? '',
                    'changed_by_user_id' => $data['updated_by_user_id'] ?? null,
                    'changed_by_name' => $data['updated_by_name'] ?? null,
                    'note' => $data['history_note'] ?? null,
                ]);
            } else {
                $openHistory = DispatchDriverStateHistory::query()
                    ->where('id_driver', $data['id_driver'])
                    ->where('id_join', $data['id_join'])
                    ->whereNull('ended_at')
                    ->latest('id')
                    ->first();

                if ($openHistory) {
                    $openHistory->update([
                        'predicted_return_at' => $data['predicted_return_at'] ?? null,
                        'last_event' => $data['last_event'] ?? '',
                        'changed_by_user_id' => $data['updated_by_user_id'] ?? null,
                        'changed_by_name' => $data['updated_by_name'] ?? null,
                        'note' => $data['history_note'] ?? $openHistory->note,
                    ]);
                }
            }

            $current->update([
                'current_status' => $data['current_status'],
                'state_started_at' => $statusChanged ? $startedAt : $current->state_started_at,
                'predicted_return_at' => $data['predicted_return_at'] ?? null,
                'last_event' => $data['last_event'] ?? '',
                'updated_by_user_id' => $data['updated_by_user_id'] ?? null,
                'updated_by_name' => $data['updated_by_name'] ?? null,
            ]);
        });

        return response()->json([
            'message' => 'Driver state saved.',
        ]);
    }
}
