<?php

namespace App\Http\Controllers;

use App\User;
use App\Beer;
use App\Order;
use Carbon\Carbon;
use App\Spectator;
use App\HistoryBeer;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Validator;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Mockery\Exception;

class BeersController extends Controller
{
    protected $urlToIkkoFron = 'http://159.224.183.143:9000/beerzha/api';

    public function index(Request $request)
    {
        try {
            $beers = Beer::select('id', 'title', 'price_stable', 'price_quotations', 'percent', 'share', 'share_count')
                ->published()
                ->get();

            $beers = $beers->each(function ($item, $key) {
                if ($item->share == "enable" && $item->share_count && $item->share_count > 0) {
                    $item->price_quotations = 0.00;
                    $item->percent = 100;
                }
            });

            return response(['data' => $beers]);

        } catch (\Exception $exception) {
            Log::warning('BeersController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'payment' => [
                'required',
                'string',
                Rule::in(['cash', 'card']),
            ],
            'price' => 'required|numeric',
            'amount' => 'required|int',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
            $beer = Beer::findOrFail($request->id);

            $history_beer_name = $beer->title;
            $history_table = $request->table;

            $name = $beer->title;
            $name = trim($name);

            $amount = $request->amount;

            $share = $beer->share;
            $count_share = $beer->share_count;

            if ($beer->share == "enable" && $count_share != 0){
                $request->price = -1;
            }else{
                $request->price = $beer->price_quotations;
            }

            $priceToIikko = $request->price;

            if ($beer->share == "enable" && $count_share != 0 && ($request->price == -1 || $request->price == -1)) {

                $amount = 1;
                $count_share = $count_share - 1;

                if ($count_share == 0){

                    $share = 'disable';
                    $count_share = 0;

                    $result['price_quotations'] = $beer->price_quotations;
                    $result['percent'] = $beer->percent;

                }else{

                    $share = 'enable';

                    $result['price_quotations'] = $beer->price_quotations;
                    $result['percent'] = $beer->percent;
                }
            }

            elseif (($beer->share == "disable" && $count_share == 0) && ($request->price == 0 || $request->price == 0.00)){
                return response()->json(['message' => 'Ціну введено некоректно або безкоштовне пиво скінчилося!'], 200);
            }
            elseif ($request->price < $beer->price_min || $request->price > $beer->price_max){
                return response()->json(['message' => 'Ціну введено некоректно!'], 200);
            }
            else{
                $result = Beer::IncreasePercent($beer->price_min, $beer->price_max, $beer->price_stable, $beer->price_quotations);
            }

            $beer->update(
                [
                    'share' => $share,
                    'share_count' => $count_share,
                    'price_quotations' => $result['price_quotations'],
                    'percent' => $result['percent'],
                    'last_order' => Carbon::now('Europe/Kiev'),
                ]
            );

            $client = new Client();
            $token = $client->request('GET', 'http://159.224.183.143:9000/beerzha/api/login?token=66923653-0D0C-41F1-B515-C74C5B965CB2')->getBody();

            $obj = json_decode($token);

            $dataOrderToIIko = array();

            $dataOrderToIIko = array_add($dataOrderToIIko, 'Name', $name);
            $dataOrderToIIko = array_add($dataOrderToIIko, 'Amount', $amount);

            $headers = [
                'Content-type' => 'application/json',
            ];

            $user = User::where('token', '=', $request->header('x-auth-token'))->first();

            $payment = 'Cash';

            if ($request->payment == 'cash') {
                $payment = 'Cash';
            }
            if ($request->payment == 'card') {
                $payment = 'Bank';
            }

            $GetOrder = [
                'OrderType' => $payment,
                'Price' => $priceToIikko,
                'Guest' => 'BEERZHA - ' . $user->phone,
                'TableNumber' => $request->table,
                'Products' => [$dataOrderToIIko]
            ];

            $client = new client();
            $request = $client->request('post', $this->urlToIkkoFron . '/orders/new?key=' . $obj->Data, [
                'headers' => $headers,
                'json' => $GetOrder,
            ]);

//          save history beers
            $history = new HistoryBeer();
            $history->name = $history_beer_name;
            $history->price = $priceToIikko;
            $history->table = $history_table;
            $history->amount = $amount;
            $history->time = Carbon::now('Europe/Kiev');
            $history->user_id = $user->id;

            $history->save();
//          save history beers

            return response()->json(['message' => 'Ваш заказ оброблено!'], 200);

        } catch (ClientException $exception) {
            Spectator::store(url()->current(), $exception->getMessage());
            return response()->json(['message' => 'Ваш заказ оброблено!'], 200);
        } catch (\Exception $exception) {
            Log::warning('BeersController@store Exception: ' . $exception->getMessage());



            return response()->json(['message' => 'Ваш заказ в обробцi!'], 500);
        }
    }
}
