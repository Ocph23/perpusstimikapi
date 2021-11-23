<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ItemKarya;


class Buku extends Model
{

    protected $table = 'Buku';

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
        'items'
    ];


    public function items()
    {
        return $this->hasMany(ItemKarya::class,'jenis_id')->where('jenis','buku');
    }

}
