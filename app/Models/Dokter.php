<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $fillable = ['poli_id', 'nama', 'spesialis', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function poli()
    {
        return $this->belongsTo(Poli::class);
    }

    public function antreans()
    {
        return $this->hasMany(Antreean::class);
    }
}
