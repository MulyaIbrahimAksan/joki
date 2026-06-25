<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Antrian — Puskesmas Sancta Vita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --sv-green: #1a6b3c; --sv-gold: #b8963e; }
        * { box-sizing: border-box; }
        body {
            background: #0a1f12;
            color: #fff;
            font-family: 'Segoe UI', sans-serif;
            min-height: 100vh;
            overflow: hidden;
        }
        header {
            background: var(--sv-green);
            padding: 12px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid var(--sv-gold);
        }
        header h1 { font-size: 1.6rem; font-weight: 700; margin: 0; }
        header .clock { font-size: 2rem; font-weight: 700; color: var(--sv-gold); font-variant-numeric: tabular-nums; }
        .poli-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 16px;
            padding: 16px;
            height: calc(100vh - 72px);
            overflow: hidden;
        }
        .poli-card {
            background: #122d1e;
            border: 1px solid #1e4d2b;
            border-radius: 16px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .poli-header {
            background: var(--sv-green);
            padding: 12px 20px;
            font-size: 1.1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .poli-kode {
            background: var(--sv-gold);
            color: #000;
            font-size: .75rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
        }
        .now-serving {
            padding: 16px 20px;
            border-bottom: 1px solid #1e4d2b;
        }
        .now-serving-label {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #6ee8a0;
            margin-bottom: 4px;
        }
        .now-number {
            font-size: 3.5rem;
            font-weight: 900;
            color: var(--sv-gold);
            line-height: 1;
        }
        .now-name { font-size: 1rem; color: #a3d9b8; margin-top: 4px; }
        .waiting-list { flex: 1; overflow: hidden; padding: 12px 20px; }
        .waiting-label {
            font-size: .65rem;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #4a8c60;
            margin-bottom: 8px;
        }
        .waiting-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px solid #1a3d25;
            font-size: .9rem;
        }
        .waiting-num {
            background: #1e4d2b;
            color: #6ee8a0;
            font-weight: 700;
            min-width: 60px;
            text-align: center;
            border-radius: 6px;
            padding: 2px 0;
            font-size: .85rem;
        }
        .kosong { color: #4a8c60; font-size: .9rem; padding: 12px 0; }
        .ticker {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--sv-gold);
            color: #000;
            font-weight: 600;
            font-size: .85rem;
            padding: 6px 0;
            overflow: hidden;
        }
        .ticker-inner {
            white-space: nowrap;
            animation: ticker 30s linear infinite;
        }
        @keyframes ticker {
            from { transform: translateX(100vw); }
            to   { transform: translateX(-100%); }
        }
        .pulse { animation: pulse 2s ease-in-out infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.6} }
        .live-dot {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #ff4444;
            border-radius: 50%;
            animation: pulse 1.5s ease-in-out infinite;
            margin-right: 6px;
        }
        .update-flash { animation: flash 0.5s ease-out; }
        @keyframes flash { 0%{background:#2d5f3d} 100%{background:transparent} }
    </style>
</head>
<body>

<header>
    <div>
        <h1><span class="live-dot"></span> Puskesmas Sancta Vita</h1>
        <div style="font-size:.8rem; color:#a3d9b8;">Sistem Antrian Digital — Live</div>
    </div>
    <div class="clock" id="clock">00:00:00</div>
</header>

<div class="poli-grid" id="poli-grid">
    @foreach($polis as $poli)
    @php
        $dilayani = $poli->antreans->firstWhere('status', 'dipanggil')
                  ?? $poli->antreans->firstWhere('status', 'dilayani');
        $menunggu = $poli->antreans->where('status', 'menunggu')->values();
    @endphp
    <div class="poli-card" id="poli-{{ $poli->id }}">
        <div class="poli-header">
            <span class="poli-kode">{{ $poli->kode_poli }}</span>
            {{ $poli->nama_poli }}
        </div>

        <div class="now-serving">
            <div class="now-serving-label">Sedang Dilayani</div>
            @if($dilayani)
            <div class="now-number pulse" id="now-num-{{ $poli->id }}">
                {{ $dilayani->nomor_antrian }}
            </div>
            <div class="now-name" id="now-name-{{ $poli->id }}">
                {{ $dilayani->pasien->nama }}
            </div>
            @else
            <div class="now-number" id="now-num-{{ $poli->id }}" style="color:#4a8c60;">---</div>
            <div class="now-name" id="now-name-{{ $poli->id }}" style="color:#4a8c60;">Belum ada</div>
            @endif
        </div>

        <div class="waiting-list">
            <div class="waiting-label">Antrian Menunggu</div>
            <div id="waiting-{{ $poli->id }}">
                @forelse($menunggu->take(8) as $a)
                <div class="waiting-item">
                    <span class="waiting-num">{{ $a->nomor_antrian }}</span>
                    <span>{{ $a->pasien->nama }}</span>
                </div>
                @empty
                <div class="kosong">Tidak ada antrian menunggu</div>
                @endforelse
                @if($menunggu->count() > 8)
                <div class="kosong">+{{ $menunggu->count() - 8 }} lainnya menunggu</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="ticker">
    <div class="ticker-inner">
        &nbsp;&nbsp;&nbsp;&nbsp;
        Selamat datang di Puskesmas Sancta Vita &nbsp;|&nbsp;
        Harap menjaga ketertiban &nbsp;|&nbsp;
        Nomor antrian dipanggil 3x, jika tidak hadir akan dilanjutkan ke nomor berikutnya &nbsp;|&nbsp;
        Terima kasih telah mempercayakan kesehatan Anda kepada kami
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Jam digital
function updateClock() {
    const now = new Date();
    document.getElementById('clock').textContent =
        now.toLocaleTimeString('id-ID', { hour12: false });
}
updateClock();
setInterval(updateClock, 1000);

// Auto-polling setiap 15 detik untuk update display
setInterval(pollDisplay, 15000);

function pollDisplay() {
    fetch('/display/data', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(updateDisplay)
        .catch(() => {});
}

function updateDisplay(polisData) {
    polisData.forEach(poli => {
        const card = document.getElementById('poli-' + poli.poli_id);
        if (!card) return;

        // Update now-serving
        const nowNum  = document.getElementById('now-num-'  + poli.poli_id);
        const nowName = document.getElementById('now-name-' + poli.poli_id);
        if (nowNum) {
            const newNum  = poli.now_serving?.nomor_antrian ?? '---';
            const newName = poli.now_serving?.nama_pasien   ?? 'Belum ada';

            if (nowNum.textContent.trim() !== newNum) {
                nowNum.textContent  = newNum;
                nowName.textContent = newName;
                nowNum.className    = poli.now_serving ? 'now-number pulse' : 'now-number';
                nowNum.style.color  = poli.now_serving ? '' : '#4a8c60';
                nowName.style.color = poli.now_serving ? '' : '#4a8c60';
                // Flash animation
                card.querySelector('.now-serving').classList.add('update-flash');
                setTimeout(() => card.querySelector('.now-serving').classList.remove('update-flash'), 500);
            }
        }

        // Update waiting list
        const waitingDiv = document.getElementById('waiting-' + poli.poli_id);
        if (waitingDiv) {
            let html = '';
            if (poli.waiting.length === 0) {
                html = '<div class="kosong">Tidak ada antrian menunggu</div>';
            } else {
                poli.waiting.forEach(w => {
                    html += `<div class="waiting-item">
                        <span class="waiting-num">${w.nomor_antrian}</span>
                        <span>${w.nama_pasien}</span>
                    </div>`;
                });
                if (poli.waiting_more > 0) {
                    html += `<div class="kosong">+${poli.waiting_more} lainnya menunggu</div>`;
                }
            }
            waitingDiv.innerHTML = html;
        }
    });
}

// Soketi realtime (fallback jika ada WebSocket)
if (typeof Echo !== 'undefined') {
    Echo.channel('antreean').listen('.status.updated', e => {
        console.log('Realtime update:', e);
        pollDisplay(); // trigger immediate refresh
    });
}
</script>
</body>
</html>
