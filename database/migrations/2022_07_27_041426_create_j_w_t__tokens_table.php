<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('jwt_tokens', function (Blueprint $table): void {
            $table->id();
            $table->string("unique_id")->unique();
            $table->unsignedBigInteger("user_id");
            $table->string("token_title");
            $table->text("restrictions")->nullable();
            $table->text("permissions")->nullable();
            $table->timestamp("expires_at")->nullable();
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
     */
    public function down(): void
    {
        Schema::dropIfExists('jwt_tokens');
    }
};
