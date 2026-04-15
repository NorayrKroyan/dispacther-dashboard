<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchShiftNote extends Model
{
    protected $table = 'dispatch_shift_notes';

    protected $fillable = [
        'id_join',
        'shift_key',
        'started_by_user_id',
        'started_by_name',
        'ended_by_user_id',
        'ended_by_name',
        'note_text',
        'is_active',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];
}
