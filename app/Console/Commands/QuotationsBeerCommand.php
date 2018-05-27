<?php

namespace App\Console\Commands;

use App\Beer;
use App\Spectator;
use Carbon\Carbon;
use Illuminate\Console\Command;

class QuotationsBeerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'quotation:beer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We recalculate quotations for beer.';

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
        $result = [];
        $beers = Beer::published()->get();

        foreach ($beers as $beer) {
            $difference = Carbon::now('Europe/Kiev')->diffInMinutes(Carbon::parse($beer->last_order, 'Europe/Kiev'));
            $difference = intval($difference);

            if ($beer->share == "enable" && $beer->share_count != 0){
                continue;
            }
            elseif (isset($difference) && ($difference >= 0 && $difference <= 2)){
                continue;
            }else{
                $result = Beer::decreasePercent($beer->price_min, $beer->price_stable, $beer->price_quotations);

                $beer->price_quotations = $result['price_quotations'];
                $beer->percent = $result['percent'];

                $beer->save();
            }
        }

        Spectator::store(url()->current(), "Запуск крона на изменение котировок биржи в: " . Carbon::now('Europe/Kiev'));
    }
}
