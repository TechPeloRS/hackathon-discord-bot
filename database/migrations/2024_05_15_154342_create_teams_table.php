<?php

use App\Enums\TeamNicheEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->uuid('code');
            $table->string('owner_email')->unique();
            $table->string('niche_type')->default(TeamNicheEnum::Unknown->value);
            $table->integer('members_count')->default(0);
            $table->string('channels_ids');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teams');
    }
};
