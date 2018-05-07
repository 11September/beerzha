<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\QuotationsBeerCommand::class,
        Commands\ChangeDayCodeCommand::class,
        Commands\ChangeCodeFrameCommand::class,
        Commands\ResetBonusesCommand::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */

    protected function schedule(Schedule $schedule)
    {
        $schedule->command('change:code')->twiceDaily('8', '23')->timezone('Europe/Kiev');
        $schedule->command('quotation:beer')->everyTenMinutes();
        $schedule->command('change:codeFrame')->dailyAt('23')->timezone('Europe/Kiev');
        $schedule->command('reset:bonuses')->quarterly()->timezone('Europe/Kiev');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
