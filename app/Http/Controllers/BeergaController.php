<?php

namespace App\Http\Controllers;

use App\Beer;
use App\Beerga;
use App\Spectator;
use Illuminate\Support\Facades\Log;

class BeergaController extends Controller
{
    public function welcome()
    {
        try {
            $ticker = Beerga::where('key', '=', 'ticker')->select('value')->first();
            $code = Beerga::where('key', '=', 'code')->select('value')->first();

            $beers = Beer::select('id', 'title', 'price_stable', 'price_quotations', 'percent', 'share', 'share_count')
                ->published()
                ->get();

        } catch (\Exception $exception) {
            Log::warning('BeerzhaController@welcome Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return view('welcome', compact('ticker', 'code', 'beers'));
    }

    public function getRunningLine()
    {
        try {
            $ticker = Beerga::where('key', '=', 'ticker')->select('value')->first();

        } catch (\Exception $exception) {
            Log::warning('BeerzhaController@getRunningLine Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response($ticker);
    }

    public function code()
    {
        try {
            $code = Beerga::where('key', '=', 'code')->select('value')->first();

            return response($code);

        } catch (\Exception $exception) {
            Log::warning('BeerzhaController@code Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }
}
