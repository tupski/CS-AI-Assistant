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

        $faq = Faq::create($validated);

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'FAQ berhasil ditambahkan',
                'data' => $faq->load('kategoriRelasi')
            ]);
        }

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

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'FAQ berhasil diupdate',
                'data' => $faq->load('kategoriRelasi')
            ]);
        }

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

    /**
     * Download template Excel untuk import
     */
    public function template()
    {
        $filename = 'template_faq.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            // Header CSV
            fputcsv($file, ['kategori_id', 'judul', 'isi']);
            // Contoh data
            fputcsv($file, ['1', 'Bagaimana cara deposit?', 'Untuk melakukan deposit, silakan login ke akun Anda...']);
            fputcsv($file, ['2', 'Berapa minimal withdraw?', 'Minimal withdraw adalah Rp 50.000...']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import FAQ dari Excel/CSV
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();

            $imported = 0;

            if ($extension === 'csv') {
                // Handle CSV
                $handle = fopen($file->getRealPath(), 'r');
                $header = fgetcsv($handle); // Skip header

                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) >= 2) {
                        $kategoriId = !empty($row[0]) && is_numeric($row[0]) ? $row[0] : null;
                        $judul = $row[1] ?? '';
                        $isi = $row[2] ?? '';

                        if (!empty($judul) && !empty($isi)) {
                            $data = [
                                'kategori_id' => $kategoriId,
                                'judul' => $judul,
                                'isi' => $isi,
                            ];

                            // Set kategori string untuk backward compatibility
                            if ($kategoriId) {
                                $kategori = Kategori::find($kategoriId);
                                if ($kategori) {
                                    $data['kategori'] = $kategori->nama;
                                }
                            }

                            Faq::create($data);
                            $imported++;
                        }
                    }
                }
                fclose($handle);
            }

            return response()->json([
                'sukses' => true,
                'imported' => $imported,
                'pesan' => "Berhasil import $imported FAQ"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }
}
