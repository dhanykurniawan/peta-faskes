<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kecamatan;
use App\Models\KabupatenKota;
use App\Models\Layanan;
use App\Models\FasilitasTempatTidur;

class Fasilitas extends Model
{
    protected $fillable = ['nama', 'tipe', 'tipe_detail', 'tipe_fktp', 'kelas', 'kode_faskes', 'kantor_cabang', 'kebutuhan_du', 'peserta_terdaftar', 'prolanis_dm', 'prolanis_ht', 'peserta_prb', 'kecamatan_id', 'kabkota_id', 'alamat', 'lat', 'lng', 'telepon', 'email', 'website', 'status_aktif'];

    protected $casts = [
        'status_aktif' => 'boolean',
        'lat' => 'float',
        'lng' => 'float',
        'kebutuhan_du' => 'integer',
        'peserta_terdaftar' => 'integer',
        'prolanis_dm' => 'integer',
        'prolanis_ht' => 'integer',
        'peserta_prb' => 'integer',
    ];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kabupatenKota()
    {
        return $this->belongsTo(KabupatenKota::class, 'kabkota_id');
    }

    public function layanans()
    {
        return $this->belongsToMany(Layanan::class, 'fasilitas_layanan')->withPivot('status_layanan');
    }

    public function fasilitasTempatTidurs()
    {
        return $this->hasMany(FasilitasTempatTidur::class);
    }
}
