<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePawnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pawns', function (Blueprint $table) {
            $table->id();
            $table->text('features');
            $table->string('pawn_type');
            $table->string('brand');
            $table->string('model');
            $table->foreignId('user_id')->constrained('users');
            $table->double('value', 8, 2)->default(0);
            $table->boolean('is_acepted')->nullable();
            $table->foreignId('asesor_id')->nullable()->constrained('users');
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
        Schema::dropIfExists('pawns');
    }
}
