<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengembalianItem extends Model
{
    protected $table = 'pengembalian_item';

    protected $fillable = [
        'id',
        'pengembalianId',
        'peminjamanItemId',
        'karyaitemId',
        'terlambat',
        'harga',
        'keadaan'
    ];

    public function Pengembalian()
    {
        return $this->belongsTo(Pengembalian::class,'pengembalianId');
    }

    public function PeminjamanItem()
    {
        return $this->belongsTo(PeminjamanItem::class,'peminjamanItemId');
    }
}
