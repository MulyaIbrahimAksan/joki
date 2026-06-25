<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poli;
use Illuminate\Http\Request;

class DokterController extends Controller
{
    public function index()
    {
        $dokters = Dokter::with('poli')->get();
        $polis   = Poli::where('is_active', true)->get();
        return view('admin.dokter.index', compact('dokters', 'polis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'poli_id'   => 'required|exists:polis,id',
            'nama'      => 'required|string|max:100',
            'spesialis' => 'nullable|string|max:100',
        ]);
        Dokter::create($request->only('poli_id', 'nama', 'spesialis'));
        return back()->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function update(Request $request, Dokter $dokter)
    {
        $request->validate([
            'poli_id'   => 'required|exists:polis,id',
            'nama'      => 'required|string|max:100',
            'spesialis' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);
        $dokter->update($request->only('poli_id', 'nama', 'spesialis', 'is_active'));
        return back()->with('success', 'Dokter berhasil diperbarui.');
    }

    public function destroy(Dokter $dokter)
    {
        $dokter->delete();
        return back()->with('success', 'Dokter berhasil dihapus.');
    }
}
