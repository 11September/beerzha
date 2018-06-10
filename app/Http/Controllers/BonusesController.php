<?php

namespace App\Http\Controllers;

use App\Dish;
use App\Order;
use App\Type;
use App\User;
use App\Bonus;
use App\Frame;
use App\Beerga;
use Carbon\Carbon;
use App\Spectator;
use Faker\Provider\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class BonusesController extends Controller
{
    public function index()
    {
        try {
            $types = Type::published()
                ->select('id', 'title', 'image')
                ->where('bonuses', 'Yes')
                ->get();

            $types = $types->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

        } catch (\Exception $exception) {
            Log::warning('TypesController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return ['data' => $types];
    }

    public function dishes(Request $request)
    {
        try {
            $dishes = Dish::select('id', 'title', 'description', 'weight', 'price', 'image')
                ->filter($request)
                ->whereHas('type', function ($query) {
                    $query->where('bonuses', '=', 'Yes');
                })
                ->published()
                ->get();

            $dishes = $dishes->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

            $dishes = $dishes->each(function ($item, $key) {
                $item['bonuses'] =  intval($item['price']);
            });

        } catch (\Exception $exception) {
            Log::warning('DishesController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response(['data' => $dishes]);
    }


    public function generate_token(Request $request)
    {
        $user = User::where('token', '=', $request->header('x-auth-token'))->first();

        if (!$user) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        $user->bonuses_token = str_random(50);
        $user->save();

        return response()->json(['message' => 'Token згенеровано!', 'data' => $user->bonuses_token], 200);
    }

    public function get_bonuses($bonusToken = null)
    {
        $user = User::where('bonuses_token', '=', $bonusToken)->first();

        if (empty($user->bonuses_token) || !$user->bonuses_token) {
            abort(404);
        }

        return view('getBonuses', compact('bonusToken'));
    }

    public function checkout($bonusToken = null)
    {
        $user = User::where('bonuses_token', '=', $bonusToken)->first();

        $orders = Order::where('payment', 'bonuses')
            ->where('user_id', $user->id)
            ->where('status', 'OPEN')
            ->with('dish')
            ->get();

        if (!$user) {
            abort(404);
        }

        $personals = Frame::all();

        return view('checkoutBonuses', compact('personals', 'bonusToken', 'orders'));
    }

    public function getbonuses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders_id' => 'required',
            'price' => 'required|numeric',
            'user_id' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $frame = Frame::where('code', $request->code)->first();
        $user = User::where('bonuses_token', '=', $request->user_id)->first();

        if (!$user || !$frame) {
            abort(403, 'Unauthorized action.');
        }

        $bonusSpend = $request->price;
        $user->bonuses = $user->bonuses - $bonusSpend;
        $user->bonuses_token = null;
        $user->save();

        foreach ($request['orders_id'] as $key => $value) {
            $dish = Order::where('id', $value)->first();
            $dish->status = 'CLOSED';
            $dish->save();
        }

        $bonus = new Bonus();
        $bonus->user_id = $user->id;
        $bonus->frame_id = $frame->id;
        $bonus->price = $request->price;
        $bonus->bonuses = $bonusSpend;
        $bonus->status = "spent";
        $bonus->date = Carbon::now('Europe/Kiev');
        $bonus->save();

        return view('bonuses-thanks');
    }

    public function obtainingBonuses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'required|numeric',
            'user_id' => 'required',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back();
        }

        $price = $request->price;

        $frame = Frame::where('code', $request->code)->first();
        $user = User::where('bonuses_token', '=', $request->user_id)->first();

        if (!$user || !$frame) {
            abort(403, 'Unauthorized action.');
        }

//      Начисление бонусов
        $percentSetting = Beerga::where('key', 'bonuses_percent')->first();
        $percent = $percentSetting->value;
        $obtainBonuses = ( intval($request->price) * $percent) / 100;

//      Убираем токен
        $user->bonuses = $user->bonuses + $obtainBonuses;
        $user->bonuses_token = null;
        $user->save();

        $bonuses = new Bonus();
        $bonuses->user_id = $user->id;
        $bonuses->frame_id = $frame->id;
        $bonuses->price = $price;
        $bonuses->bonuses = $obtainBonuses;
        $bonuses->status = "accrued";
        $bonuses->date = Carbon::now()->toDateTimeString();
        $bonuses->save();

        return view('bonuses-thanks');
    }

    public function check_tokens(Request $request)
    {
        try {

        $user = User::where('token', '=', $request->header('x-auth-token'))->first();

        if (!$user) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        $currentTime = Carbon::now()->toDateTimeString();

        } catch (\Exception $exception) {
            Log::warning('DishesController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response()->json(['message' => 'Данi отриманi!', 'bonuses' => $user->bonuses, 'currentTime' => $currentTime], 200);
    }
}
