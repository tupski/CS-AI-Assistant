<?php

namespace App\Http\Controllers;

use App\Models\Peraturan;
use Illuminate\Http\Request;

class PeraturanController extends Controller
{
    /**
     * Tampilkan daftar peraturan
     */
    public function index(Request $request)
    {
        $query = Peraturan::query();

        // Filter berdasarkan tipe
        if ($request->filled('tipe')) {
            $query->tipe($request->tipe);
        }

        // Filter berdasarkan prioritas
        if ($request->filled('prioritas')) {
            $query->prioritas($request->prioritas);
        }

        // Search berdasarkan judul atau isi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        $peraturans = $query->urutan()->get();

        // Group by tipe untuk tampilan
        $peraturansGrouped = $peraturans->groupBy('tipe');

        return view('peraturan.index', compact('peraturansGrouped', 'peraturans'));
    }

    /**
     * Simpan peraturan baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:umum,larangan,wajib,tips',
            'prioritas' => 'required|in:tinggi,normal,rendah',
            'aktif' => 'boolean',
            'urutan' => 'integer|min:0',
        ], [
            'judul.required' => 'Judul peraturan wajib diisi',
            'isi.required' => 'Isi peraturan wajib diisi',
            'tipe.required' => 'Tipe peraturan wajib dipilih',
            'tipe.in' => 'Tipe peraturan tidak valid',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'prioritas.in' => 'Prioritas tidak valid',
        ]);

        $validated['aktif'] = $request->has('aktif');

        Peraturan::create($validated);

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan berhasil ditambahkan');
    }

    /**
     * Update peraturan
     */
    public function update(Request $request, Peraturan $peraturan)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'tipe' => 'required|in:umum,larangan,wajib,tips',
            'prioritas' => 'required|in:tinggi,normal,rendah',
            'aktif' => 'boolean',
            'urutan' => 'integer|min:0',
        ], [
            'judul.required' => 'Judul peraturan wajib diisi',
            'isi.required' => 'Isi peraturan wajib diisi',
            'tipe.required' => 'Tipe peraturan wajib dipilih',
            'tipe.in' => 'Tipe peraturan tidak valid',
            'prioritas.required' => 'Prioritas wajib dipilih',
            'prioritas.in' => 'Prioritas tidak valid',
        ]);

        $validated['aktif'] = $request->has('aktif');

        $peraturan->update($validated);

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan berhasil diupdate');
    }

    /**
     * Hapus peraturan
     */
    public function destroy(Peraturan $peraturan)
    {
        $peraturan->delete();

        return redirect()->route('peraturan.index')
            ->with('success', 'Peraturan berhasil dihapus');
    }
}
