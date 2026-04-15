<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispatchJobDriverAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DispatchAssignmentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_join' => ['required', 'integer'],
            'id_driver' => ['required', 'integer'],
            'assigned_by_user_id' => ['nullable', 'integer'],
            'assigned_by_name' => ['nullable', 'string', 'max:120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $assignment = DispatchJobDriverAssignment::query()->updateOrCreate(
            [
                'id_join' => $data['id_join'],
                'id_driver' => $data['id_driver'],
            ],
            [
                'assigned_by_user_id' => $data['assigned_by_user_id'] ?? null,
                'assigned_by_name' => $data['assigned_by_name'] ?? null,
                'assigned_at' => now(),
                'unassigned_at' => null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        return response()->json([
            'message' => 'Driver assignment saved.',
            'assignment' => $assignment,
        ]);
    }

    public function deactivate(Request $request, int $assignmentId): JsonResponse
    {
        $assignment = DispatchJobDriverAssignment::query()->findOrFail($assignmentId);
        $assignment->update([
            'is_active' => false,
            'unassigned_at' => now(),
        ]);

        return response()->json([
            'message' => 'Driver assignment deactivated.',
        ]);
    }
}
