<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poli extends Model
{
    protected $fillable = ['nama_poli', 'kode_poli', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function dokters()
    {
        return $this->hasMany(Dokter::class);
    }

    public function antreans()
    {
        return $this->hasMany(Antreean::class);
    }

    public function antreanHariIni()
    {
        return $this->antreans()->where('tanggal', now()->toDateString());
    }

    public function nomorAntreianBerikutnya(): string
    {
        $last = $this->antreans()
            ->where('tanggal', now()->toDateString())
            ->orderByDesc('id')
            ->first();

        $nomor = $last ? ((int) substr($last->nomor_antrian, 1)) + 1 : 1;

        return $this->kode_poli . str_pad($nomor, 3, '0', STR_PAD_LEFT);
    }
}
