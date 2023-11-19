<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('moves', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('round_id')->references('id')->on('rounds');
            $table->foreignUuid('player_id')->references('id')->on('players');
            $table->foreignUuid('user_id')->references('id')->on('users');
            $table->integer('score')->nullable();
            $table->json('payload')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('moves');
    }
}
