<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Spectator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DishesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $dishes = Dish::select('id', 'title', 'description', 'weight', 'price', 'image')
                ->filter($request)
                ->published()
                ->get();

            $dishes = $dishes->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

        } catch (\Exception $exception) {
            Log::warning('DishesController@index Exception: '. $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response(['data' => $dishes]);
    }
}

