<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Kategori;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    /**
     * Tampilkan daftar FAQ
     */
    public function index(Request $request)
    {
        $query = Faq::with('kategori');

        // Filter berdasarkan kategori jika ada
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Search berdasarkan judul atau isi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('isi', 'like', "%{$search}%");
            });
        }

        $faqs = $query->latest()->get();
        $kategoris = Kategori::aktif()->urutan()->get();

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->has('ajax')) {
            return response()->json([
                'sukses' => true,
                'data' => $faqs
            ]);
        }

        return view('faq.index', compact('faqs', 'kategoris'));
    }

    /**
     * Simpan FAQ baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'nullable|exists:kategori,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul FAQ wajib diisi',
            'isi.required' => 'Isi FAQ wajib diisi',
            'kategori_id.exists' => 'Kategori tidak valid',
        ]);

        // Set kategori string dari nama kategori untuk backward compatibility
        if ($request->filled('kategori_id')) {
            $kategori = Kategori::find($request->kategori_id);
            $validated['kategori'] = $kategori->nama;
        }

        Faq::create($validated);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil ditambahkan');
    }

    /**
     * Update FAQ
     */
    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'kategori_id' => 'nullable|exists:kategori,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul FAQ wajib diisi',
            'isi.required' => 'Isi FAQ wajib diisi',
            'kategori_id.exists' => 'Kategori tidak valid',
        ]);

        // Set kategori string dari nama kategori untuk backward compatibility
        if ($request->filled('kategori_id')) {
            $kategori = Kategori::find($request->kategori_id);
            $validated['kategori'] = $kategori->nama;
        } else {
            $validated['kategori'] = null;
        }

        $faq->update($validated);

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil diupdate');
    }

    /**
     * Hapus FAQ
     */
    public function destroy(Request $request, Faq $faq)
    {
        $faq->delete();

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'FAQ berhasil dihapus'
            ]);
        }

        return redirect()->route('faq.index')
            ->with('success', 'FAQ berhasil dihapus');
    }
}
