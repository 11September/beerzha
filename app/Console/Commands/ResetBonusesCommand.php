<?php

namespace App\Console\Commands;

use App\User;
use App\Spectator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ResetBonusesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:bonuses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We reset bonuses.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::select('id', 'bonuses')->get();

        foreach ($users as $user) {
            $user->bonuses = 0;
            $user->save();
        }

        Spectator::store(url()->current(), "Запуск крона на обнуление бонусов: " . Carbon::now('Europe/Kiev'));
    }
}
