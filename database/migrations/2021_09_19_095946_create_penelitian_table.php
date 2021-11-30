<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenelitianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penelitian', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lokasi_id");  
            $table->string('kode');
            $table->string('npm');
            $table->string('penulis');
            $table->string('jurusan');
            $table->string('pembimbing');
            $table->string('judul');
            $table->string('topik');
            $table->enum('jenis', ['kp', 'skripsi', 'tesis']);
            $table->integer('tahun');
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
        Schema::dropIfExists('penelitian');
    }
}
