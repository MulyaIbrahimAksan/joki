<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        // Admin default — login: admin@sanctavita.com / 12345678
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@sanctavita.com'],
            [
                'name'       => 'Admin Sancta Vita',
                'password'   => Hash::make('12345678'),
                'role'       => 'admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Poli awal
        $polis = [
            ['nama_poli' => 'Poli Umum',       'kode_poli' => 'U'],
            ['nama_poli' => 'Poli Gigi',        'kode_poli' => 'G'],
            ['nama_poli' => 'Poli Anak',        'kode_poli' => 'A'],
            ['nama_poli' => 'Poli Kandungan',   'kode_poli' => 'K'],
            ['nama_poli' => 'Poli Mata',        'kode_poli' => 'M'],
        ];

        foreach ($polis as $p) {
            DB::table('polis')->updateOrInsert(
                ['kode_poli' => $p['kode_poli']],
                array_merge($p, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // Dokter awal
        $poliUmum = DB::table('polis')->where('kode_poli', 'U')->value('id');
        $poliGigi = DB::table('polis')->where('kode_poli', 'G')->value('id');
        $poliAnak = DB::table('polis')->where('kode_poli', 'A')->value('id');

        $dokters = [
            ['poli_id' => $poliUmum, 'nama' => 'dr. Budi Santoso',    'spesialis' => 'Umum'],
            ['poli_id' => $poliUmum, 'nama' => 'dr. Sari Dewi',       'spesialis' => 'Umum'],
            ['poli_id' => $poliGigi, 'nama' => 'drg. Rina Wahyuni',   'spesialis' => 'Gigi'],
            ['poli_id' => $poliAnak, 'nama' => 'dr. Ahmad Fauzi, Sp.A', 'spesialis' => 'Anak'],
        ];

        foreach ($dokters as $d) {
            DB::table('dokters')->updateOrInsert(
                ['nama' => $d['nama']],
                array_merge($d, [
                    'is_active'  => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }

    public function down(): void
    {
        DB::table('dokters')->truncate();
        DB::table('polis')->truncate();
        DB::table('users')->truncate();
    }
};
