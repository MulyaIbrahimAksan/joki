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
        $barcodeCode  = 'SV-' . date('Ymd') . '-' . $nomorAntrian;

        // Generate barcode pakai GD native (tidak butuh library tambahan)
        $image = $this->generateBarcode($barcodeCode);

        $antreean = Antreean::create([
            'pasien_id'     => $pasien->id,
            'poli_id'       => $request->poli_id,
            'dokter_id'     => $request->dokter_id ?: null,
            'nomor_antrian' => $nomorAntrian,
            'barcode_code'  => $barcodeCode,
            'barcode_image' => $image,
            'status'        => 'menunggu',
            'tanggal'       => today(),
        ]);

        // Broadcast ke Soketi — dibungkus try-catch
        // supaya tidak crash kalau Soketi belum jalan
        try {
            broadcast(new AntreeanUpdated($antreean))->toOthers();
        } catch (\Throwable $e) {
            // Soketi belum aktif — abaikan, antrian tetap tersimpan
            \Log::warning('Broadcast gagal (Soketi mungkin belum jalan): ' . $e->getMessage());
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

    /**
     * Generate barcode Code 128 sebagai base64 PNG pakai GD native.
     * Tidak butuh install library apapun.
     */
    private function generateBarcode(string $text, int $width = 2, int $height = 60): string
    {
        $code128B = [
            ' '=>0,'!'=>1,'"'=>2,'#'=>3,'$'=>4,'%'=>5,'&'=>6,"'"=>7,
            '('=>8,')'=>9,'*'=>10,'+'=>11,','=>12,'-'=>13,'.'=>14,'/'=>15,
            '0'=>16,'1'=>17,'2'=>18,'3'=>19,'4'=>20,'5'=>21,'6'=>22,'7'=>23,
            '8'=>24,'9'=>25,':'=>26,';'=>27,'<'=>28,'='=>29,'>'=>30,'?'=>31,
            '@'=>32,'A'=>33,'B'=>34,'C'=>35,'D'=>36,'E'=>37,'F'=>38,'G'=>39,
            'H'=>40,'I'=>41,'J'=>42,'K'=>43,'L'=>44,'M'=>45,'N'=>46,'O'=>47,
            'P'=>48,'Q'=>49,'R'=>50,'S'=>51,'T'=>52,'U'=>53,'V'=>54,'W'=>55,
            'X'=>56,'Y'=>57,'Z'=>58,'['=>59,'\\'=>60,']'=>61,'^'=>62,'_'=>63,
            '`'=>64,'a'=>65,'b'=>66,'c'=>67,'d'=>68,'e'=>69,'f'=>70,'g'=>71,
            'h'=>72,'i'=>73,'j'=>74,'k'=>75,'l'=>76,'m'=>77,'n'=>78,'o'=>79,
            'p'=>80,'q'=>81,'r'=>82,'s'=>83,'t'=>84,'u'=>85,'v'=>86,'w'=>87,
            'x'=>88,'y'=>89,'z'=>90,'{'=>91,'|'=>92,'}'=>93,'~'=>94,
        ];

        $patterns = [
            '11011001100','11001101100','11001100110','10010011000','10010001100',
            '10001001100','10011001000','10011000100','10001100100','11001001000',
            '11001000100','11000100100','10110011100','10011011100','10011001110',
            '10111001100','10011101100','10011100110','11001110010','11001011100',
            '11001001110','11011100100','11001110100','11101101110','11101001100',
            '11100101100','11100100110','11101100100','11100110100','11100110010',
            '11011011000','11011000110','11000110110','10100011000','10001011000',
            '10001000110','10110001000','10001101000','10001100010','11010001000',
            '11000101000','11000100010','10110111000','10110001110','10001101110',
            '10111011000','10111000110','10001110110','11101110110','11010001110',
            '11000101110','11011101000','11011100010','11011101110','11101011000',
            '11101000110','11100010110','11101101000','11101100010','11100011010',
            '11101111010','11001000010','11110001010','10100110000','10100001100',
            '10010110000','10010000110','10000101100','10000100110','10110010000',
            '10110000100','10011010000','10011000010','10000110100','10000110010',
            '11000010010','11001010000','11110111010','11000010100','10001111010',
            '10100111100','10010111100','10010011110','10111100100','10011110100',
            '10011110010','11110100100','11110010100','11110010010','11011011110',
            '11011110110','11110110110','10101111000','10100011110','10001011110',
            '10111101000','10111100010','11110101000','11110100010','10111011110',
            '10111101110','11101011110','11110101110','11010000100','11010010000',
            '11010011100','1100011101011',
        ];

        $symbols  = [104]; // START B
        $checksum = 104;
        foreach (str_split($text) as $i => $char) {
            $val       = $code128B[$char] ?? 0;
            $symbols[] = $val;
            $checksum += ($i + 1) * $val;
        }
        $symbols[] = $checksum % 103; // checksum
        $symbols[] = 106;             // STOP

        $bars = '';
        foreach ($symbols as $sym) {
            $bars .= $patterns[$sym] ?? '';
        }
        $bars .= '11';

        $barCount = strlen($bars);
        $imgW     = $barCount * $width + 20;
        $imgH     = $height + 20;

        $img   = imagecreate($imgW, $imgH);
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        imagefill($img, 0, 0, $white);

        $x = 10;
        foreach (str_split($bars) as $bit) {
            if ($bit === '1') {
                imagefilledrectangle($img, $x, 10, $x + $width - 1, 10 + $height, $black);
            }
            $x += $width;
        }

        ob_start();
        imagepng($img);
        $png = ob_get_clean();
        imagedestroy($img);

        return base64_encode($png);
    }
}
