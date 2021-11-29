<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemkaryaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('itemkarya', function (Blueprint $table) {
            $table->id();
            $table->text('nomorseri');
            $table->foreignId('jenis_id');
            $table->enum('jenis', ['buku', 'penelitian']);
            $table->enum('statuspinjam', ['tersedia', 'dipinjam', 'dipesan']);
            $table->enum('keadaan', ['baik', 'rusak', 'hilang']);
            $table->text('catatan');
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
        Schema::dropIfExists('itemkarya');
    }
}
