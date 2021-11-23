<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $fillable = [
        'id',
        'peminjamanid',
        'tanggal',
    ];

    protected $appends = [
        'peminjaman',
        'items'
    ];

    public function peminjaman()
    {
        return $this->hasOne(Peminjaman::class,'id', 'peminjamanid');
    }

    public function Items()
    {
        return $this->hasMany(PengembalianItem::class,'pengembalianId', 'id');
    }
}
