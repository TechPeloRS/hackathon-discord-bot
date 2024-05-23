<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')
                ->constrained('teams')
                ->cascadeOnDelete();
            $table->string('discord_id')->unique();
            $table->string('role_type');
            $table->string('github_username')->nullable();
            $table->timestamps();

            $table->index('discord_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
