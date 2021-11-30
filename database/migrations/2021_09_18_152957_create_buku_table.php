<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBukuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buku', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lokasi_id");    
            $table->string('kode');
            $table->string('judul');
            $table->string('edisi')->nullable();
            $table->string('bibliografi')->nullable();
            $table->string('penulis');
            $table->string('kategori');
            $table->string('isbn');
            $table->string('penerbit');
            $table->integer('tahun');
            $table->string('kota');
            $table->text('deskripsi');
            $table->text('cover')->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buku');
    }
}
