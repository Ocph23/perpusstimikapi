<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PesananItem extends Model
{
    protected $table = 'pesananitem';
    protected $fillable = [
        'pesananid',
        'karyaitemid'
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class,'id','pesananid');
    }

    public function karyaitem()
    {
        return $this->belongsTo(ItemKarya::class,'karyaitemid');
    }

  
}
