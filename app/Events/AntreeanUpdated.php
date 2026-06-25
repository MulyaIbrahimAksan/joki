<?php

namespace App\Events;

use App\Models\Antreean;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntreeanUpdated implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(public Antreean $antreean) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('antreean'),
            new Channel('poli.' . $this->antreean->poli_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'id'             => $this->antreean->id,
            'nomor_antrian'  => $this->antreean->nomor_antrian,
            'status'         => $this->antreean->status,
            'poli_id'        => $this->antreean->poli_id,
            'nama_poli'      => $this->antreean->poli->nama_poli,
            'nama_pasien'    => $this->antreean->pasien->nama,
        ];
    }
}
