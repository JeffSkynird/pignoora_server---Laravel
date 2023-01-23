<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->string('message');
            $table->foreignId('pawn_id')->constrained('pawns');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('admin_id')->nullable()->constrained('users');
            $table->boolean('is_complete')->default(false);
            $table->string('type')->default('message');
            $table->double('value', 8, 2)->default(0);
            $table->boolean('is_admin')->default(false);
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
        Schema::dropIfExists('chats');
    }
}
