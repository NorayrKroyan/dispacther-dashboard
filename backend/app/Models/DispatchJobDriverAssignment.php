<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchJobDriverAssignment extends Model
{
    protected $table = 'dispatch_job_driver_assignments';

    protected $fillable = [
        'id_join',
        'id_driver',
        'assigned_by_user_id',
        'assigned_by_name',
        'assigned_at',
        'unassigned_at',
        'is_active',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'unassigned_at' => 'datetime',
        'is_active' => 'boolean',
    ];
}
