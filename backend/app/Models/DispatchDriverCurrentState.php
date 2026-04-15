<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDriverCurrentState extends Model
{
    protected $table = 'dispatch_driver_current_states';

    protected $fillable = [
        'id_driver',
        'id_join',
        'current_status',
        'state_started_at',
        'predicted_return_at',
        'last_event',
        'updated_by_user_id',
        'updated_by_name',
    ];

    protected $casts = [
        'state_started_at' => 'datetime',
        'predicted_return_at' => 'datetime',
    ];
}
