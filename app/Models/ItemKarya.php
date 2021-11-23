<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemKarya extends Model
{

    protected $table = 'itemkarya';

    protected $fillable = [
        'nomorseri',
        'jenis_id',
        'jenis',
        'statuspinjam',
        'keadaan',
        'catatan',
    ];
   
    public function parent()
    {
        if($this->jenis=='buku')
            return $this->belongsTo(Buku::class,'jenis_id','id');
        else{
            return $this->belongsTo(Penelitian::class,'jenis_id','id');
        }
    }

}
