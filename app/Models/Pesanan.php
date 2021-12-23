<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'id',
        'kode',
        'anggotaid',
        'tanggal',
        'status'
       
    ];

    protected $appends = [
        'anggota',
        'items'
    ];

    public function anggota()
    {
        return $this->hasOne(Anggota::class,'id','anggotaid');
    }

    public function items()
    {
        return $this->hasMany(PesananItem::class,'pesananid');
    }
}
