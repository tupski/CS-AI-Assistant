<?php

namespace App\Http\Controllers;

use App\Models\Informasi;
use App\Models\Kategori;
use Illuminate\Http\Request;

class InformasiController extends Controller
{
    /**
     * Tampilkan daftar Informasi Umum
     */
    public function index(Request $request)
    {
        $query = Informasi::with('kategoriRelasi');

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

        $informasi = $query->latest()->get();
        $kategoris = Kategori::aktif()->urutan()->get();

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->has('ajax')) {
            // Transform data untuk frontend
            $data = $informasi->map(function ($info) {
                return [
                    'id' => $info->id,
                    'judul' => $info->judul,
                    'isi' => $info->isi,
                    'kategori_id' => $info->kategori_id,
                    'kategori' => $info->kategoriRelasi ? [
                        'id' => $info->kategoriRelasi->id,
                        'nama' => $info->kategoriRelasi->nama,
                        'warna' => $info->kategoriRelasi->warna,
                        'icon' => $info->kategoriRelasi->icon,
                    ] : null,
                    'created_at' => $info->created_at,
                    'updated_at' => $info->updated_at,
                ];
            });

            return response()->json([
                'sukses' => true,
                'data' => $data
            ]);
        }

        return view('informasi.index', compact('informasi', 'kategoris'));
    }

    /**
     * Simpan Informasi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'nullable|exists:kategori,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul Informasi wajib diisi',
            'isi.required' => 'Isi Informasi wajib diisi',
            'kategori_id.exists' => 'Kategori tidak valid',
        ]);

        // Set kategori string dari nama kategori untuk backward compatibility
        if ($request->filled('kategori_id')) {
            $kategori = Kategori::find($request->kategori_id);
            $validated['kategori'] = $kategori->nama;
        }

        $informasi = Informasi::create($validated);

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'Informasi berhasil ditambahkan',
                'data' => $informasi->load('kategoriRelasi')
            ]);
        }

        return redirect()->route('informasi.index')
            ->with('success', 'Informasi berhasil ditambahkan');
    }

    /**
     * Update Informasi
     */
    public function update(Request $request, Informasi $informasi)
    {
        $validated = $request->validate([
            'kategori_id' => 'nullable|exists:kategori,id',
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
        ], [
            'judul.required' => 'Judul Informasi wajib diisi',
            'isi.required' => 'Isi Informasi wajib diisi',
            'kategori_id.exists' => 'Kategori tidak valid',
        ]);

        // Set kategori string dari nama kategori untuk backward compatibility
        if ($request->filled('kategori_id')) {
            $kategori = Kategori::find($request->kategori_id);
            $validated['kategori'] = $kategori->nama;
        } else {
            $validated['kategori'] = null;
        }

        $informasi->update($validated);

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'Informasi berhasil diupdate',
                'data' => $informasi->load('kategoriRelasi')
            ]);
        }

        return redirect()->route('informasi.index')
            ->with('success', 'Informasi berhasil diupdate');
    }

    /**
     * Hapus Informasi
     */
    public function destroy(Request $request, Informasi $informasi)
    {
        $informasi->delete();

        // Jika request AJAX, return JSON
        if ($request->ajax() || $request->expectsJson()) {
            return response()->json([
                'sukses' => true,
                'pesan' => 'Informasi berhasil dihapus'
            ]);
        }

        return redirect()->route('informasi.index')
            ->with('success', 'Informasi berhasil dihapus');
    }

    /**
     * Download template Excel untuk import
     */
    public function template()
    {
        $filename = 'template_informasi.csv';
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
     * Import Informasi dari Excel/CSV
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

                            Informasi::create($data);
                            $imported++;
                        }
                    }
                }
                fclose($handle);
            }

            return response()->json([
                'sukses' => true,
                'imported' => $imported,
                'pesan' => "Berhasil import $imported Informasi"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'sukses' => false,
                'pesan' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }
}

