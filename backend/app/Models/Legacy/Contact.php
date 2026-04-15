<?php

namespace App\Models\Legacy;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact';
    protected $primaryKey = 'id_contact';
    public $incrementing = true;
    public $timestamps = false;

    protected $guarded = [];

    public function getFullNameAttribute(): string
    {
        return trim((string) ($this->first_name ?? '') . ' ' . (string) ($this->last_name ?? ''));
    }
}
