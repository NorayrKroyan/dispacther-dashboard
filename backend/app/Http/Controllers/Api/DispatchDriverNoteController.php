<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DispatchDriverNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DispatchDriverNoteController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'id_driver' => ['required', 'integer'],
            'id_join' => ['required', 'integer'],
            'note_text' => ['nullable', 'string'],
            'updated_by_user_id' => ['nullable', 'integer'],
            'updated_by_name' => ['nullable', 'string', 'max:120'],
        ]);

        $note = DispatchDriverNote::query()->firstOrNew([
            'id_driver' => $data['id_driver'],
            'id_join' => $data['id_join'],
        ]);

        if (!$note->exists) {
            $note->created_by_user_id = $data['updated_by_user_id'] ?? null;
            $note->created_by_name = $data['updated_by_name'] ?? null;
        }

        $note->note_text = $data['note_text'] ?? '';
        $note->updated_by_user_id = $data['updated_by_user_id'] ?? null;
        $note->updated_by_name = $data['updated_by_name'] ?? null;
        $note->save();

        return response()->json([
            'message' => 'Driver note saved.',
            'note' => $note,
        ]);
    }
}
