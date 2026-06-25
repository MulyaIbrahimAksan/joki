<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Request;

class PoliController extends Controller
{
    public function index()
    {
        $polis = Poli::withCount(['antreans as total_hari_ini' => fn($q) => $q->hariIni()])->get();
        return view('admin.poli.index', compact('polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:100',
            'kode_poli' => 'required|string|max:10|unique:polis',
        ]);
        Poli::create($request->only('nama_poli', 'kode_poli'));
        return back()->with('success', 'Poli berhasil ditambahkan.');
    }

    public function update(Request $request, Poli $poli)
    {
        $request->validate([
            'nama_poli' => 'required|string|max:100',
            'kode_poli' => 'required|string|max:10|unique:polis,kode_poli,' . $poli->id,
            'is_active' => 'boolean',
        ]);
        $poli->update($request->only('nama_poli', 'kode_poli', 'is_active'));
        return back()->with('success', 'Poli berhasil diperbarui.');
    }

    public function destroy(Poli $poli)
    {
        $poli->delete();
        return back()->with('success', 'Poli berhasil dihapus.');
    }
}
