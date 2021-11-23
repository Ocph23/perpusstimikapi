<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnggotaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
      return [
        'id' => $this->id,
        'nomor_induk' => $this->nomor_induk,
        'nama' => $this->nama ,
        'tempat_lahir' => $this->tempat_lahir ,
        'tanggal_lahir' => $this->tanggal_lahir ,
        'jenis_kelamin' => $this->jenis_kelamin,
        'kewarganegaraan' => $this->kewarganegaraan ,
        'agama' => $this->agama ,
        'jenis' => $this->jenis ,
        'aktif' => $this->aktif,
        'kodeanggota'=>'STIMIK'.sprintf('%04d',$this->id),
        'created_at' => $this->created_at,
        'updated_at' => $this->updated_at,
      ];
    }
}
