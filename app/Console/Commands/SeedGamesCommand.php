<?php

namespace App\Console\Commands;

use App\Models\GameLogic;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class SeedGamesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:seed-logics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the Games from Files to game_logics table';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        collect(glob(app_path('Support/GamePolicies/*.php')))
            ->map(function ($filename) {
                $className = (string) Str::of($filename)->after('/app')->start('App')->before('.php')->replace('/', '\\');
                $gameName = (string) Str::of($className)->afterLast('\\')->before('Policy');

                if (!$gameName) {
                    return;
                }

                /** @var Metric $metric */
                $metric = GameLogic::updateOrCreate([
                    'policy' => $className,
                ], [
                    'name' => $gameName,
                ]);
                $this->info('Seeded Game: ' . $gameName);
            });

        $this->info('Game logics seeding completed');
    }
}
