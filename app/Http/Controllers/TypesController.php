<?php

namespace App\Http\Controllers;

use App\Type;
use App\Spectator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TypesController extends Controller
{
    public function index(Request $request)
    {
        try {
            $types = Type::select('id', 'title', 'image')
                ->filter($request)
                ->published()
                ->get();

            $types = $types->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

        } catch (\Exception $exception) {
            Log::warning('TypesController@index Exception: '. $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return ['data' => $types];
    }

    public function categories(Request $request)

    {
        try {
            $types = Type::select('id', 'title', 'image')
                ->filter($request)
                ->published()
                ->get();

            $types = $types->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

        } catch (\Exception $exception) {
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return ['data' => $types];
    }
}