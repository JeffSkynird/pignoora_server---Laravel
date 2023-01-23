<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pawn_id')->constrained('pawns');
            $table->foreignId('asesor_id')->constrained('users');
            $table->double('amount', 8, 2)->default(0);
            $table->integer('term')->default(0);
            $table->double('interest', 8, 2)->default(0);
            $table->string('pay_type');
            $table->date('start_date');
            $table->string('status')->default('active');
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
        Schema::dropIfExists('credits');
    }
}
