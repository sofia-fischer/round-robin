<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('agent')->nullable();
            $table->unsignedInteger('group_id')->nullable();
            $table->unsignedInteger('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('color')->nullable();
            $table->unsignedInteger('counter')->nullable();
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
        Schema::dropIfExists('players');
    }
}
