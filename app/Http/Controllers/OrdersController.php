<?php

namespace App\Http\Controllers;

use App\User;
use App\Dish;
use App\Order;
use App\Beerga;
use App\Spectator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Mockery\Exception;

class OrdersController extends Controller
{
    protected $urlToIkkoFront = 'http://159.224.183.143:9000/beerzha/api';

    public function index(Request $request)
    {
        try {

            $user_id = User::select('id')->where('token', '=', $request->header('x-auth-token'))->first();

            $orders = Order::where('status', 'OPEN')
                ->whereHas('dish', function ($query) use ($user_id) {
                    $query->where('user_id', '=', $user_id->id);
                })
                ->with(array('dish' => function ($query) {
                    $query->select('id', 'title', 'price', 'image');
                }))
                ->get();

            return response([
                'data' => $orders
            ]);

        } catch (\Exception $exception) {
            Log::warning('OrdersController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int',
            'amount' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
            $user_id = User::select('id')->where('token', '=', $request->header('x-auth-token'))->first();

            $dish = Dish::findOrFail($request->id);
            $order = new Order();
            $order->user_id = $user_id->id;
            $order->dish_id = $dish->id;
            $order->amount = $request->amount;
            $order->status = "OPEN";
            $order->address = $request->address;
            $order->price = ($request->amount * $dish->price);
            $order->save();
            return response()->json(['message' => 'Ваш заказ оброблено!'], 200);

        } catch (\Exception $exception) {
            Log::warning('OrderController@store Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required',
            'orders.*.id' => 'required|int',
            'orders.*.amount' => 'required|int',
            'payment' => [
                'required',
                'string',
                Rule::in(['cash', 'card', 'home']),
            ],
            'address' => 'string|min:10',
            'comment' => ''
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        $request_table = 80;

        try {
            $dataOrderToIIko = array();

            foreach ($request['orders'] as $key => $value) {

                // Data to IKKO
                $dish = Dish::where('id', $value['id'])->first();

                $name = $dish->title;
                $name = trim($name);

                $dataOrderToIIko[$key] = ['Name' => $name, 'Amount' => $value['amount']];
                // Data to IKKO

                $user_id = User::select('id')->where('token', '=', $request->header('x-auth-token'))->first();
                $order = new Order();
                $order->user_id = $user_id->id;
                $order->dish_id = $value['id'];
                $order->amount = $value['amount'];
                $order->seat = $request_table;
                $order->payment = $request->payment;
                $order->address = $request->address;
                $order->note = $request->comment;

                if ($request->payment == "home") {
                    $order->status = "HOME";
                } else {
                    $order->status = "CLOSED";
                }

                $order->price = ($value['amount'] * $order->dish->price);

                $order->save();
            }

            /**
             *   POST IKKO
             */

            $client = new Client();
            $token = $client->request('GET', 'http://159.224.183.143:9000/beerzha/api/login?token=66923653-0D0C-41F1-B515-C74C5B965CB2')->getBody();

            $obj = json_decode($token);

            $headers = [
                'Content-type' => 'application/json',
            ];

            $user = User::where('token', '=', $request->header('x-auth-token'))->first();

            $payment = 'Cash';
            $destination = null;

            if ($request->payment == 'cash') {
                $payment = 'Cash';
            }
            if ($request->payment == 'card') {
                $payment = 'Bank';
            }
            if ($request->payment == 'home') {
                $payment = 'Додому';
            }

            if ($request->payment == 'home' && $request->address) {
                $destination = "- " . $request->address;
            }

            Spectator::store(url()->current(), "BEERZHA - $user->phone" . " " . $destination, $payment);

            $GetOrder = [

                'Price' => 0.00,
                'OrderType' => $payment,
                'Guest' => "BEERZHA - $user->phone" . " " . $destination,
                'TableNumber' => $request_table,
                'Products' => $dataOrderToIIko
            ];

            $client = new client();
            $request = $client->request('post', $this->urlToIkkoFront . '/orders/new?key=' . $obj->Data, [
                'headers' => $headers,
                'json' => $GetOrder,
            ]);

            /**
             *   POST IKKO END
             */

            return response()->json(['message' => 'Ваш заказ оброблено!'], 200);

        } catch (ClientException $exception) {
            Spectator::store(url()->current(), $exception->getMessage());
            return response()->json(['message' => 'Ваш заказ оброблено!'], 200);
        } catch (\Exception $exception) {
            Log::warning('OrderController@order Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function order_bonuses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'orders' => 'required',
            'orders.*.id' => 'required|int',
            'orders.*.amount' => 'required|int',
            'payment' => [
                'required',
                'string',
                Rule::in(['bonuses']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
//          Difine all nesessary variables
            $request_table = 80;
            $orders = array();

//          Check time
            $startTime = Carbon::createFromFormat('H:i:s', '12:00:00');
//            $endTime = Carbon::createFromFormat('H:i:s', '17:00:00');
            $endTime = Carbon::createFromFormat('H:i:s', '17:00:00');


            $allowable_time = (Carbon::now()->between($startTime, $endTime));

            if (!$allowable_time) {
                return response()->json(['message' => 'Час для покупки не валiдний!'], 200);
            }

//          Check bonuses
            $user = User::select('id', 'bonuses')->where('token', '=', $request->header('x-auth-token'))->first();
            $total_price = 0;
            $total_item = 0;
            $userBonuses = $user->bonuses;

            foreach ($request['orders'] as $key => $value) {
                $dish = Dish::where('id', $value['id'])->first();
                $total_item = $value['amount'] * intval($dish->price);
                $total_price += $total_item;
            }

            if ($userBonuses < $total_price) {
                return response()->json(['message' => 'Бонусiв недостатьньо для заказу'], 200);
            }

//          Clear opened orders
             $old_orders = Order::where('user_id', $user->id)
                 ->where('status', 'OPEN')
                 ->where('payment', 'bonuses')
                 ->get();

            foreach ($old_orders as $old_order) {
                $old_order->status = 'CANCELED';
                $old_order->save();
            }

//          Store temporary order
            foreach ($request['orders'] as $key => $value) {
                $dish = Dish::where('id', $value['id'])->first();
                $order = new Order();
                $order->user_id = $user->id;
                $order->dish_id = $dish->id;
                $order->amount = $value['amount'];
                $order->price = intval(($value['amount'] * intval($dish->price)));
                $order->seat = $request_table;
                $order->payment = "bonuses";
                $order->status = "OPEN";
                $order->save();

                array_push($orders, ['id' => $order->id]);
            }

//            Generate token
            $user->bonuses_token = str_random(50);
            $user->save();

            return response()->json(['message' => 'Ваш заказ оброблено!', 'data' => $user->bonuses_token, 'orders' => $orders], 200);
        } catch (\Exception $exception) {
            Log::warning('OrderController@order Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }


    public function code(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|int',
            'table' => 'required|int',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {

            $code = Beerga::where('key', '=', 'code')->first();
            $table = Beerga::where('key', '=', 'tables')->first();

            if ($request->code == intval($code->value) && $request->table > 0 && $request->table <= intval($table->value)) {
                return response()->json(['message' => 'Дані введено корректно.'], 200);
            } else {
                return response()->json(['message' => 'Помилка, некоректно введенні данні. Перевірте код дня або номер столику!'], 401);
            }

        } catch (\Exception $exception) {
            Log::warning('OrderController@code Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }
}
