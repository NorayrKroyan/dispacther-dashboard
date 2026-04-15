<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $table = 'driver';
    protected $primaryKey = 'id_driver';
    public $incrementing = true;
    public $timestamps = false;

    protected $guarded = [];

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'id_contact', 'id_contact');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'id_vehicle', 'id_vehicle');
    }
}
