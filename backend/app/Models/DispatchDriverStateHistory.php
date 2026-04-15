<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDriverStateHistory extends Model
{
    protected $table = 'dispatch_driver_state_histories';

    protected $fillable = [
        'id_driver',
        'id_join',
        'status',
        'started_at',
        'ended_at',
        'predicted_return_at',
        'last_event',
        'changed_by_user_id',
        'changed_by_name',
        'note',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'predicted_return_at' => 'datetime',
    ];
}
