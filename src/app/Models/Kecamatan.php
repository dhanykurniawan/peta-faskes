<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\KabupatenKota;
use App\Models\Fasilitas;

class Kecamatan extends Model
{
    protected $table = 'kecamatan';

    protected $fillable = ['kabkota_id', 'nama', 'kode_bps', 'populasi'];

    public function kabupatenKota()
    {
        return $this->belongsTo(KabupatenKota::class, 'kabkota_id');
    }

    public function fasilitas()
    {
        return $this->hasMany(Fasilitas::class);
    }

    public function fktp()
    {
        return $this->hasMany(Fasilitas::class)->where('tipe', 'FKTP');
    }

    public function fkrtl()
    {
        return $this->hasMany(Fasilitas::class)->where('tipe', 'FKRTL');
    }
}
