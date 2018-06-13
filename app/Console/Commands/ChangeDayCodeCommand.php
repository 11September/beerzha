<?php

namespace App\Console\Commands;

use App\Beerga;
use App\Spectator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeDayCodeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:code';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change pin code every day';

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
        $digits = (int)Beerga::getKeyLength();

        if(!$digits){
            $digits = 3;
        }

        if($digits > 5){
            $digits = 3;
        }

        $code = Beerga::where('key', "code")->first();

        $code->value = rand(pow(10, $digits-1), pow(10, $digits)-1);

//        Spectator::store(url()->current(), "Запуск крона на изменение кода дня в: " . Carbon::now('Europe/Kiev') . " на значение: " , $code->value);

        $code->save();
    }
}
