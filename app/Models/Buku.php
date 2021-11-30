<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ItemKarya;


class Buku extends Model
{

    protected $table = 'buku';

    protected $fillable = [
        'judul',
        'kode',
        'penerbit',
        'kategori',
        'edisi',
        'bibliografi',
        'isbn',
        'deskripsi',
        'tahun',
        'kota',
        'cover',
        'penulis',
        'lokasi_id',
    ];

    protected $appends = [
        'items'
    ];


    public function items()
    {
        return $this->hasMany(ItemKarya::class,'jenis_id')->where('jenis','buku');
    }

    public function lokasi()
    {
        return $this->hasOne(Lokasi::class,'id', 'lokasi_id');
    }

}
