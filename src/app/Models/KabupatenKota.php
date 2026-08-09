<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kecamatan;
use App\Models\Fasilitas;

class KabupatenKota extends Model
{
    protected $table = 'kabupaten_kota';

    protected $fillable = ['nama', 'kode_bps', 'populasi'];

    public function kecamatans()
    {
        return $this->hasMany(Kecamatan::class, 'kabkota_id');
    }

    public function fasilitas()
    {
        return $this->hasMany(Fasilitas::class, 'kabkota_id');
    }
}
