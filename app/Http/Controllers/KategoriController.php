<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    /**
     * Tampilkan daftar kategori
     */
    public function index()
    {
        $kategoris = Kategori::withCount('faq')->urutan()->get();
        return view('kategori.index', compact('kategoris'));
    }

    /**
     * Simpan kategori baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama',
            'warna' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
            'urutan' => 'integer|min:0',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique' => 'Nama kategori sudah digunakan',
            'warna.required' => 'Warna wajib dipilih',
        ]);

        // Auto-generate slug
        $validated['slug'] = Str::slug($validated['nama']);
        $validated['aktif'] = $request->has('aktif');

        Kategori::create($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }

    /**
     * Update kategori
     */
    public function update(Request $request, Kategori $kategori)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255|unique:kategori,nama,' . $kategori->id,
            'warna' => 'required|string|max:20',
            'icon' => 'nullable|string|max:50',
            'deskripsi' => 'nullable|string',
            'aktif' => 'boolean',
            'urutan' => 'integer|min:0',
        ], [
            'nama.required' => 'Nama kategori wajib diisi',
            'nama.unique' => 'Nama kategori sudah digunakan',
            'warna.required' => 'Warna wajib dipilih',
        ]);

        // Auto-generate slug jika nama berubah
        if ($kategori->nama !== $validated['nama']) {
            $validated['slug'] = Str::slug($validated['nama']);
        }

        $validated['aktif'] = $request->has('aktif');

        $kategori->update($validated);

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil diupdate');
    }

    /**
     * Hapus kategori
     */
    public function destroy(Kategori $kategori)
    {
        // Cek apakah kategori masih digunakan di FAQ
        if ($kategori->faq()->count() > 0) {
            return redirect()->route('kategori.index')
                ->with('error', 'Kategori tidak bisa dihapus karena masih digunakan di FAQ');
        }

        $kategori->delete();

        return redirect()->route('kategori.index')
            ->with('success', 'Kategori berhasil dihapus');
    }
}
