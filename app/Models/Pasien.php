<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pasien extends Model
{
    protected $fillable = ['nama', 'usia', 'no_hp', 'keluhan'];

    public function antreans()
    {
        return $this->hasMany(Antreean::class);
    }
}
