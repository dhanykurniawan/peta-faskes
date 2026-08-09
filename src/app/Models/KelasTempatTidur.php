<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelasTempatTidur extends Model
{
    public const DEFAULT_CLASSES = [
        ['kode' => 'kelas_1', 'nama' => 'Kelas 1'],
        ['kode' => 'kelas_2', 'nama' => 'Kelas 2'],
        ['kode' => 'kelas_3', 'nama' => 'Kelas 3'],
        ['kode' => 'icu', 'nama' => 'ICU'],
        ['kode' => 'iccu', 'nama' => 'ICCU'],
        ['kode' => 'nicu', 'nama' => 'NICU'],
        ['kode' => 'hcu', 'nama' => 'HCU'],
        ['kode' => 'perinatologi', 'nama' => 'Perinatologi'],
    ];

    protected $fillable = ['kode', 'nama', 'urutan'];

    public function fasilitasTempatTidurs()
    {
        return $this->hasMany(FasilitasTempatTidur::class);
    }
}
