<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeminjamanItem extends Model
{

    protected $table = 'peminjaman_item';

    protected $fillable = [
        'peminjaman_id',
        'karyaitem_id',
        'tanggal_kembali',
        'statuskembali'
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class,'peminjaman_id');
    }

    public function ItemKarya()
    {
        return $this->belongsTo(ItemKarya::class,'karyaitem_id');
    }
    
}

