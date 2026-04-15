<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDriverTrackingSnapshot extends Model
{
    protected $table = 'dispatch_driver_tracking_snapshots';

    protected $fillable = [
        'id_driver',
        'id_join',
        'last_event',
        'miles_to_job',
        'eta_to_deliver_minutes',
        'gps_lat',
        'gps_lng',
        'gps_recorded_at',
        'source_table',
        'source_id',
        'synced_at',
    ];

    protected $casts = [
        'miles_to_job' => 'decimal:2',
        'eta_to_deliver_minutes' => 'integer',
        'gps_lat' => 'decimal:7',
        'gps_lng' => 'decimal:7',
        'gps_recorded_at' => 'datetime',
        'synced_at' => 'datetime',
    ];
}
