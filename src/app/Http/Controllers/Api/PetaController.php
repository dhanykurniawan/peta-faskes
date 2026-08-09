<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KabupatenKota;
use App\Models\Kecamatan;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

use App\Models\Layanan;
class PetaController extends Controller
{
    // List 5 kab/kota
    public function kabkota()
    {
        $data = KabupatenKota::withCount([
            'fasilitas as fktp_count' => fn($q) => $q->where('tipe', 'FKTP'),
            'fasilitas as fkrtl_count' => fn($q) => $q->where('tipe', 'FKRTL'),
        ])->get();

        return response()->json($data);
    }

    // FKRTL di kab/kota tertentu
    public function faskesByKabkota($id)
    {
        $faskes = Fasilitas::with(['kecamatan', 'layanans'])
            ->where('kabkota_id', $id)
            ->where('tipe', 'FKRTL')
            ->where('status_aktif', true)
            ->get();

        return response()->json($faskes);
    }

    // FKTP & FKRTL di kecamatan tertentu
    public function faskesByKecamatan($id)
    {
        $kecamatan = Kecamatan::with('kabupatenKota')->findOrFail($id);

        $faskes = Fasilitas::with('layanans')->where('kecamatan_id', $id)->where('status_aktif', true)->orderBy('tipe')->get()->groupBy('tipe');

        return response()->json([
            'kecamatan' => $kecamatan->nama,
            'kabkota' => $kecamatan->kabupatenKota->nama,
            'fktp' => $faskes->get('FKTP', collect()),
            'fkrtl' => $faskes->get('FKRTL', collect()),
        ]);
    }

    public function cariKecamatan(Request $request)
    {
        $nama = $request->query('nama');
        $kabkotaId = $request->query('kabkota_id');

        $kecamatan = Kecamatan::where('kabkota_id', $kabkotaId)
            ->whereRaw('LOWER(nama) LIKE ?', ['%' . strtolower($nama) . '%'])
            ->first();

        if (!$kecamatan) {
            return response()->json(['message' => 'Kecamatan tidak ditemukan'], 404);
        }

        $faskes = Fasilitas::with('layanans')->where('kecamatan_id', $kecamatan->id)->where('status_aktif', true)->orderBy('tipe')->get()->groupBy('tipe');

        return response()->json([
            'kecamatan' => $kecamatan->nama,
            'populasi'  => $kecamatan->populasi,  // ← tambah ini
            'kabkota' => $kecamatan->kabupatenKota->nama,
            'fktp' => $faskes->get('FKTP', collect()),
            'fkrtl' => $faskes->get('FKRTL', collect()),
        ]);
    }

    public function markersByKabkota($id)
    {
        $faskes = Fasilitas::where('kabkota_id', $id)->whereNotNull('lat')->whereNotNull('lng')->where('status_aktif', true)->select('id', 'nama', 'tipe', 'tipe_detail', 'kelas', 'alamat', 'lat', 'lng')->get();

        return response()->json($faskes);
    }

    public function cariFaskes(Request $request)
    {
        $q = trim($request->query('q', ''));

        if (strlen($q) < 2) {
            return response()->json([]);
        }

        $results = Fasilitas::with(['kecamatan', 'kabupatenKota', 'layanans', 'fasilitasTempatTidurs.kelasTempatTidur'])
            ->where('nama', 'like', "%{$q}%")
            ->limit(20)
            ->get()
            ->map(fn($f) => array_merge($f->toArray(), ['tipe' => $f->tipe]));

        $fktp  = $results->filter(fn($f) => $f['tipe'] === 'FKTP')->values();
        $fkrtl = $results->filter(fn($f) => $f['tipe'] === 'FKRTL')->values();

        return response()->json([
            'fktp'  => $fktp,
            'fkrtl' => $fkrtl,
        ]);
    }
}
