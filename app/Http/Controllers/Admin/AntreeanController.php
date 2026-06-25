<?php

namespace App\Http\Controllers\Admin;

use App\Events\AntreeanUpdated;
use App\Http\Controllers\Controller;
use App\Models\Antreean;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AntreeanController extends Controller
{
    public function index(Request $request)
    {
        $today  = now()->toDateString();
        $poliId = $request->poli_id;

        // Cache poli list (rarely changes)
        $polis = Cache::remember('active_polis', 300, function () {
            return Poli::where('is_active', true)->get();
        });

        // Cache key includes poli filter and date
        $cacheKey = 'antrian_index_' . $today . '_' . ($poliId ?? 'all');
        $antreans = Cache::remember($cacheKey, 15, function () use ($poliId, $today) {
            $query = Antreean::with([
                    'pasien:id,nama,usia,keluhan',
                    'poli:id,nama_poli',
                    'dokter:id,nama'
                ])
                ->where('tanggal', $today)
                ->orderBy('nomor_antrian');

            if ($poliId) {
                $query->where('poli_id', $poliId);
            }

            return $query->paginate(20);
        });

        return view('admin.antrian.index', compact('antreans', 'polis', 'poliId'));
    }

    public function scan()
    {
        return view('admin.antrian.scan');
    }

    public function cariBarcode(Request $request)
    {
        $antreean = Antreean::with(['pasien', 'poli', 'dokter'])
            ->where('barcode_code', $request->barcode)
            ->where('tanggal', now()->toDateString())
            ->first();

        if (!$antreean) {
            return response()->json(['error' => 'Barcode tidak ditemukan'], 404);
        }

        return response()->json([
            'id'            => $antreean->id,
            'nomor_antrian' => $antreean->nomor_antrian,
            'nama_pasien'   => $antreean->pasien->nama,
            'usia'          => $antreean->pasien->usia,
            'keluhan'       => $antreean->pasien->keluhan,
            'poli'          => $antreean->poli->nama_poli,
            'dokter'        => $antreean->dokter?->nama ?? '-',
            'status'        => $antreean->status,
        ]);
    }

    public function updateStatus(Request $request, Antreean $antreean)
    {
        $request->validate([
            'status' => 'required|in:menunggu,dipanggil,dilayani,selesai,batal',
        ]);

        $data = ['status' => $request->status, 'user_id' => auth()->id()];

        if ($request->status === 'dipanggil') {
            $data['dipanggil_at'] = now();
        }
        if ($request->status === 'selesai') {
            $data['selesai_at'] = now();
        }

        $antreean->update($data);
        $antreean->load(['pasien', 'poli', 'dokter']);

        // Clear display cache so display updates immediately
        Cache::forget('display_data');
        Cache::forget('display_initial_' . $today);
        
        // Clear dashboard cache
        $today = now()->toDateString();
        Cache::forget('dashboard_stats_' . $today);
        Cache::forget('dashboard_polis_' . $today);
        Cache::forget('dashboard_terbaru_' . $today);
        
        // Clear antrian index cache
        Cache::forget('antrian_index_' . $today . '_all');
        Cache::forget('antrian_index_' . $today . '_' . $antreean->poli_id);

        broadcast(new AntreeanUpdated($antreean))->toOthers();

        return response()->json(['success' => true, 'status' => $antreean->status]);
    }

    public function reset(Request $request)
    {
        $request->validate(['poli_id' => 'required|exists:polis,id']);

        Antreean::hariIni()
            ->where('poli_id', $request->poli_id)
            ->whereIn('status', ['menunggu', 'dipanggil'])
            ->update(['status' => 'batal']);

        return back()->with('success', 'Antrian berhasil direset.');
    }

    public function display()
    {
        $today = now()->toDateString();
        
        $polis = Cache::remember('display_initial_' . $today, 15, function () use ($today) {
            return Poli::where('is_active', true)->with(['antreans' => function ($q) use ($today) {
                $q->where('tanggal', $today)
                  ->whereIn('status', ['menunggu', 'dipanggil', 'dilayani'])
                  ->with('pasien:id,nama')
                  ->orderBy('nomor_antrian');
            }])->get();
        });

        return view('display.index', compact('polis'));
    }

    /**
     * JSON endpoint for display auto-refresh (polling).
     */
    public function displayData()
    {
        $data = Cache::remember('display_data', 10, function () {
            $polis = Poli::where('is_active', true)->with(['antreans' => function ($q) {
                $q->hariIni()->aktif()->with('pasien')->orderBy('nomor_antrian');
            }])->get();

            return $polis->map(function ($poli) {
                $dilayani = $poli->antreans->firstWhere('status', 'dipanggil')
                          ?? $poli->antreans->firstWhere('status', 'dilayani');

                $menunggu = $poli->antreans->where('status', 'menunggu')->values();

                return [
                    'poli_id'    => $poli->id,
                    'nama_poli'  => $poli->nama_poli,
                    'kode_poli'  => $poli->kode_poli,
                    'now_serving' => $dilayani ? [
                        'nomor_antrian' => $dilayani->nomor_antrian,
                        'nama_pasien'   => $dilayani->pasien->nama,
                        'status'        => $dilayani->status,
                    ] : null,
                    'waiting' => $menunggu->take(8)->map(fn($a) => [
                        'nomor_antrian' => $a->nomor_antrian,
                        'nama_pasien'   => $a->pasien->nama,
                    ])->toArray(),
                    'waiting_more' => max(0, $menunggu->count() - 8),
                ];
            })->toArray();
        });

        return response()->json($data);
    }
}
