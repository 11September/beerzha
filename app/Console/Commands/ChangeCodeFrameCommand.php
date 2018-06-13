<?php

namespace App\Console\Commands;

use App\Frame;
use App\Spectator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ChangeCodeFrameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'change:codeFrame';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change pin code frame every day';

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
        $frames = Frame::all();

        foreach ($frames as $frame) {
            $frame->code = random_int ( 1000 , 9999 );
            $frame->save();
        }

//        Spectator::store(url()->current(), "Запуск крона на изменение кода персонала в: " . Carbon::now('Europe/Kiev'));
    }
}
