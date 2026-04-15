<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class JobJoin extends Model
{
    protected $table = 'join';
    protected $primaryKey = 'id_join';
    public $incrementing = true;
    public $timestamps = false;

    protected $guarded = [];
}
