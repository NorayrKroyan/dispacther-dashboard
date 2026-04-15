<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $table = 'vehicle';
    protected $primaryKey = 'id_vehicle';
    public $incrementing = true;
    public $timestamps = false;

    protected $guarded = [];
}
