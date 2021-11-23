<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePengembalianItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pengembalian_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalianId');
            $table->foreignId('peminjamanItemId');
            $table->integer('terlambat');
            $table->double('harga');
            $table->text('keadaan');
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
        Schema::dropIfExists('pengembalian_item');
    }
}
