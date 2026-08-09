<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FasilitasTempatTidur extends Model
{
    protected $fillable = ['fasilitas_id', 'kelas_tempat_tidur_id', 'jumlah'];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }

    public function kelasTempatTidur()
    {
        return $this->belongsTo(KelasTempatTidur::class);
    }
}
