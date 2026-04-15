<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispatchShiftNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DispatchShiftNoteController extends Controller
{
    public function index(int $idJoin): JsonResponse
    {
        $notes = DispatchShiftNote::query()
            ->where('id_join', $idJoin)
            ->orderByDesc('is_active')
            ->orderByDesc('started_at')
            ->get();

        return response()->json([
            'shift_notes' => $notes,
        ]);
    }

    public function upsertActive(Request $request, int $idJoin): JsonResponse
    {
        $data = $request->validate([
            'shift_key' => ['required', 'string', 'max:30'],
            'started_by_user_id' => ['nullable', 'integer'],
            'started_by_name' => ['required', 'string', 'max:120'],
            'note_text' => ['nullable', 'string'],
        ]);

        $note = DB::transaction(function () use ($idJoin, $data) {
            $existing = DispatchShiftNote::query()
                ->where('id_join', $idJoin)
                ->where('shift_key', $data['shift_key'])
                ->where('started_by_name', $data['started_by_name'])
                ->where('is_active', true)
                ->latest('id')
                ->first();

            if ($existing) {
                $existing->update([
                    'note_text' => $data['note_text'] ?? '',
                ]);

                return $existing->fresh();
            }

            return DispatchShiftNote::query()->create([
                'id_join' => $idJoin,
                'shift_key' => $data['shift_key'],
                'started_by_user_id' => $data['started_by_user_id'] ?? null,
                'started_by_name' => $data['started_by_name'],
                'note_text' => $data['note_text'] ?? '',
                'is_active' => true,
                'started_at' => now(),
            ]);
        });

        return response()->json([
            'message' => 'Shift note saved.',
            'shift_note' => $note,
        ]);
    }

    public function close(Request $request, int $idJoin, int $noteId): JsonResponse
    {
        $data = $request->validate([
            'ended_by_user_id' => ['nullable', 'integer'],
            'ended_by_name' => ['nullable', 'string', 'max:120'],
        ]);

        $note = DispatchShiftNote::query()
            ->where('id_join', $idJoin)
            ->findOrFail($noteId);

        $note->update([
            'is_active' => false,
            'ended_by_user_id' => $data['ended_by_user_id'] ?? null,
            'ended_by_name' => $data['ended_by_name'] ?? null,
            'ended_at' => now(),
        ]);

        return response()->json([
            'message' => 'Shift note closed.',
        ]);
    }
}
