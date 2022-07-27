<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jwt_tokens', function (Blueprint $table) {
            $table->id();
            $table->string("unique_id")->unique();
            $table->unsignedBigInteger("user_id");
            $table->string("token_title");
            $table->text("restrictions");
            $table->text("permissions");
            $table->timestamp("expires_at");
            $table->timestamp("last_used_at")->nullable();
            $table->timestamp("refreshed_at")->nullable();
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")
            ->onUpdate("cascade")
            ->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jwt_tokens');
    }
};
