<?php

namespace App\Providers;

use App\Commands\SpawnRoomsCommand;
use App\Console\Commands\LoadHackathonCommand;
use Laracord\LaracordServiceProvider;

class BotServiceProvider extends LaracordServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        $this->commands([
            LoadHackathonCommand::class,
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        parent::register();
    }
}
