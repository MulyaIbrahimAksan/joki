<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Antreean extends Model
{
    protected $table = 'antreans';

    protected $fillable = [
        'pasien_id', 'poli_id', 'dokter_id', 'user_id',
        'nomor_antrian', 'barcode_code', 'barcode_image',
        'status', 'dipanggil_at', 'selesai_at', 'tanggal',
    ];

    protected $casts = [
        'dipanggil_at' => 'datetime',
        'selesai_at'   => 'datetime',
        'tanggal'      => 'date',
    ];

    public function pasien()  { return $this->belongsTo(Pasien::class); }
    public function poli()    { return $this->belongsTo(Poli::class); }
    public function dokter()  { return $this->belongsTo(Dokter::class); }
    public function user()    { return $this->belongsTo(User::class); }

    public function scopeHariIni($query)
    {
        return $query->where('tanggal', now()->toDateString());
    }

    public function scopeAktif($query)
    {
        return $query->whereIn('status', ['menunggu', 'dipanggil', 'dilayani']);
    }

    public function getBadgeColorAttribute(): string
    {
        return match($this->status) {
            'menunggu'  => 'warning',
            'dipanggil' => 'info',
            'dilayani'  => 'primary',
            'selesai'   => 'success',
            'batal'     => 'danger',
            default     => 'secondary',
        };
    }
}
