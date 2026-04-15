<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchDriverNote extends Model
{
    protected $table = 'dispatch_driver_notes';

    protected $fillable = [
        'id_driver',
        'id_join',
        'note_text',
        'created_by_user_id',
        'created_by_name',
        'updated_by_user_id',
        'updated_by_name',
    ];
}
