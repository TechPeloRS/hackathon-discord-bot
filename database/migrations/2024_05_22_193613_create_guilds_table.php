<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guilds', function (Blueprint $table) {
            $table->id();
            $table->string('provider_id');
            $table->boolean('main_server');
            $table->string('invite_url');
            $table->integer('teams_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guilds');
    }
};
