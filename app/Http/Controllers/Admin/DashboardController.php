<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Antreean;
use App\Models\Poli;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        // Single query for all stats (reduces 4 queries → 1)
        $rawStats = Cache::remember('dashboard_stats_' . $today, 30, function () use ($today) {
            return Antreean::where('tanggal', $today)
                ->selectRaw("
                    COUNT(*) as total_hari_ini,
                    COUNT(CASE WHEN status = 'menunggu' THEN 1 END) as menunggu,
                    COUNT(CASE WHEN status IN ('dipanggil','dilayani') THEN 1 END) as dilayani,
                    COUNT(CASE WHEN status = 'selesai' THEN 1 END) as selesai
                ")
                ->first();
        });

        $stats = [
            'total_hari_ini' => $rawStats->total_hari_ini,
            'menunggu'       => $rawStats->menunggu,
            'dilayani'       => $rawStats->dilayani,
            'selesai'        => $rawStats->selesai,
        ];

        // Single query for poli stats
        $polis = Cache::remember('dashboard_polis_' . $today, 30, function () use ($today) {
            return Poli::where('is_active', true)
                ->select('polis.*')
                ->selectSub(
                    Antreean::selectRaw('COUNT(*)')
                        ->whereColumn('antreans.poli_id', 'polis.id')
                        ->where('tanggal', $today)
                        ->where('status', 'menunggu'),
                    'antrian_menunggu'
                )
                ->selectSub(
                    Antreean::selectRaw('COUNT(*)')
                        ->whereColumn('antreans.poli_id', 'polis.id')
                        ->where('tanggal', $today),
                    'antrian_total'
                )
                ->get();
        });

        // Single query for recent antreans
        $terbaru = Cache::remember('dashboard_terbaru_' . $today, 30, function () use ($today) {
            return Antreean::with(['pasien', 'poli'])
                ->where('tanggal', $today)
                ->latest()
                ->take(10)
                ->get();
        });

        return view('admin.dashboard', compact('stats', 'polis', 'terbaru'));
    }
}
