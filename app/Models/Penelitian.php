<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penelitian extends Model
{

    protected $table = 'penelitian';

    protected $fillable = [
        'kode',
        'npm',
        'penulis',
        'jurusan',
        'pembimbing',
        'judul',
        'topik',
        'jenis',
        'tahun',
        'deskripsi',
        'cover',
        'lokasi_id',
        'items',
    ];

    public function items()
    {
        return $this->hasMany(ItemKarya::class,'jenis_id')->where('jenis','penelitian');
    }

    public function lokasi(){
        return $this->hasOne(Lokasi::class,'id','lokasi_id');
    }
    
}
