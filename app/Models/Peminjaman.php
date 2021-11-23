<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{

    protected $table = 'peminjaman';

    protected $fillable = [
        'anggotaid',
        'keterangan',
        'status',
    ];

    protected $appends = [
        'anggota',
        'items'
    ];

    public function anggota()
    {
        return $this->hasOne(Anggota::class,'id', 'anggotaid');
    }

    public function Items()
    {
        return $this->hasMany(PeminjamanItem::class,'peminjaman_id', 'id');
    }
    
}

