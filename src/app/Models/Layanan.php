<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Fasilitas;

class Layanan extends Model
{
    protected $table = 'layanan';
    protected $fillable = ['nama_layanan'];

    public function fasilitas()
    {
        return $this->belongsToMany(Fasilitas::class, 'fasilitas_layanan')->withPivot('status_layanan');
    }
}
