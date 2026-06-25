<?php

namespace App\Http\Controllers;

use App\Events\AntreeanUpdated;
use App\Models\Antreean;
use App\Models\Pasien;
use App\Models\Poli;
use Illuminate\Http\Request;

class PasienController extends Controller
{
    public function index()
    {
        $polis = Poli::where('is_active', true)->get();
        return view('pasien.daftar', compact('polis'));
    }

    public function getDokter(Poli $poli)
    {
        $dokters = $poli->dokters()->where('is_active', true)->get(['id', 'nama', 'spesialis']);
        return response()->json($dokters);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:100',
            'usia'      => 'required|integer|min:0|max:150',
            'no_hp'     => 'nullable|string|max:20',
            'keluhan'   => 'required|string|max:500',
            'poli_id'   => 'required|exists:polis,id',
            'dokter_id' => 'nullable|exists:dokters,id',
        ]);

        $pasien = Pasien::create($request->only('nama', 'usia', 'no_hp', 'keluhan'));

        $poli         = Poli::findOrFail($request->poli_id);
        $nomorAntrian = $poli->nomorAntreianBerikutnya();

        $antreean = Antreean::create([
            'pasien_id'     => $pasien->id,
            'poli_id'       => $request->poli_id,
            'dokter_id'     => $request->dokter_id ?: null,
            'nomor_antrian' => $nomorAntrian,
            'status'        => 'menunggu',
            'tanggal'       => today(),
        ]);

        // Broadcast ke Soketi — dibungkus try-catch
        try {
            broadcast(new AntreeanUpdated($antreean))->toOthers();
        } catch (\Throwable $e) {
            \Log::warning('Broadcast gagal: ' . $e->getMessage());
        }

        return redirect()->route('pasien.tiket', $antreean->id);
    }

    public function tiket(Antreean $antreean)
    {
        $antreean->load(['pasien', 'poli', 'dokter']);

        $posisi = Antreean::hariIni()
            ->where('poli_id', $antreean->poli_id)
            ->where('status', 'menunggu')
            ->where('id', '<=', $antreean->id)
            ->count();

        return view('pasien.tiket', compact('antreean', 'posisi'));
    }

    public function cekStatus(Antreean $antreean)
    {
        $antrianDepan = Antreean::hariIni()
            ->where('poli_id', $antreean->poli_id)
            ->where('status', 'menunggu')
            ->where('id', '<', $antreean->id)
            ->count();

        return response()->json([
            'status'        => $antreean->status,
            'nomor_antrian' => $antreean->nomor_antrian,
            'antrian_depan' => $antrianDepan,
        ]);
    }

}
