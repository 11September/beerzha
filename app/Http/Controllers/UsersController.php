<?php

namespace App\Http\Controllers;

use App\Beerga;
use App\User;
use App\Widget;
use App\Preorder;
use App\Spectator;
use App\HistoryBeer;
use App\SocialAccount;
use App\Mail\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use TCG\Voyager\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class UsersController extends Controller
{
    public $isNewUser = false;

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:255',
            'password' => 'required|string|min:6|max:255',
        ]);


        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        if (Auth::user()) {
            $user = Auth::user();

            $result = array();
            $result = array_add($result, 'token', $user->token);

            return response($result);
        }

        $user = User::where('phone', $request->login)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

//                auth()->login($user);
                if (Auth::attempt(['email' => $request->login, 'password' => $user->password])) {
                    $result = array();
                    $result = array_add($result, 'token', $user->token);

                    return response($result);
                } else {
                    return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
                }
            }
            return response()->json(['message' => 'Користувача немає або логін / пароль не підходять'], 401);
        }

        $user = User::where('email', $request->login)->first();
        if ($user) {
            if (Hash::check($request->password, $user->password)) {

//                auth()->login($user);

                if (Auth::attempt(['email' => $request->login, 'password' => $request->password])) {
                    $result = array();
                    $result = array_add($result, 'token', $user->token);

                    return response($result);
                } else {
                    return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
                }
            }
        }

        return response()->json(['message' => 'Користувача немає або логін / пароль не підходять'], 401);
    }

    public function logout()
    {
        try {

            Auth::logout();

        } catch (\Exception $exception) {
            Log::warning('UsersController@logout Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response()->json(['success' => true], 200);
    }

    public function register(Request $request)
    {
        $validator_right = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|min:10',
            'password' => 'required|string|min:6|max:255',
        ]);

        $validator_present = Validator::make($request->all(), [
            'email' => 'unique:users',
            'phone' => 'unique:users',
        ]);

        if ($validator_right->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        if ($validator_present->fails()) {
            return response()->json(['message' => 'Користувач з таким номером телефону / email вже існує!'], 404);
        }

        try {
            $user = new User();
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->token = bcrypt($request->email);
            $user->save();

            Auth::attempt(array('email' => $user->email, 'password' => $user->password), true);
//            auth()->login($user);

            return response(['token' => $user->token]);

        } catch (\Exception $exception) {
            Log::warning('UsersController@register Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
        ]);

        $validator_exist = Validator::make($request->all(), [
            'email' => 'exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        if ($validator_exist->fails()) {
            return response()->json(['message' => 'Користувача не існує!'], 404);
        }

        try {

            $user = User::where('email', $request->email)->first();

            $new_password = $this->generatePassword();

            $user->password = bcrypt($new_password);

            \Mail::to($request->email)->send(new ResetPassword($user, $new_password));

            $user->save();

        } catch (\Exception $exception) {
            Log::warning('UsersController@resetPassword Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response()->json(['success' => true], 200);
    }

    public function generatePassword($length = 8)
    {
        $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }

    public function changePersonalInfo(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|string|min:10',
            'email' => 'string|email|max:255',
            'birthday' => 'required|date',
            'gender' => [
                'string',
                Rule::in(['male', 'female']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні aбо такого номеру не існує!'], 400);
        }

        try {
            $user = User::where('token', '=', $request->header('x-auth-token'))->first();

            $user->phone = $request->phone;

            $user->save();

        } catch (\Exception $exception) {
            Log::warning('UsersController@changePersonalInfo Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response(["data" => $user]);
    }

    public function preOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'time' => ['required', 'regex:^(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)$^'],
            'date' => 'required|date',
            'phone' => 'string',
            'name' => 'string',
            'event' => 'string',
            'wishes' => 'string',
            'count_people' => 'required|int',
            'callback' => [
                'string',
                Rule::in(['YES', 'NO']),
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
            $preorder = new Preorder;

            $user_id = User::select('id')->where('token', '=', $request->header('x-auth-token'))->first();

            $preorder->callback = $request->callback;
            $preorder->time = $request->time;
            $preorder->date = $request->date;

            $preorder->name = $request->name;
            $preorder->event = $request->event;
            $preorder->wishes = $request->wishes;
            $preorder->phone = $request->phone;

            $preorder->count_people = $request->count_people;
            $preorder->user_id = $user_id->id;
            $preorder->save();

        } catch (\Exception $exception) {
            Log::warning('UsersController@preorder Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
        return response()->json(['message' => 'Ваше замовлення прийнято!'], 200);
    }

    public function social(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'provider' => [
                'required',
                'string',
                Rule::in(['google', 'facebook']),
            ],
            'token' => 'required|string',
            'phone' => 'string|min:10',
            'email' => 'string|email|min:10',
            'first_name' => 'string|min:3',
            'last_name' => 'string|min:3',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
            $user = $this->createOrGetUser($request->provider, $request->token, $request->phone, $request->email, $request->first_name, $request->last_name);

            Auth::attempt(array('token' => $user->token, 'password' => $user->password), true);
//            auth()->login($user);

        } catch (\Exception $exception) {
            Log::warning('UsersController@social Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response(["token" => $user->token, "isNewUser" => $this->isNewUser], 200);
    }

    public function createOrGetUser($provider, $token, $phone, $email, $first_name, $last_name)
    {
        $user = null;

        if (!$phone) {
            $phone = null;
        }

        if (!$email) {
            $email = null;
        }

        if (!$first_name) {
            $first_name = null;
        }

        if (!$last_name) {
            $last_name = null;
        }

        $account = SocialAccount::select('user_id', 'provider_user_id')
            ->where('provider', $provider)
            ->where('provider_user_id', $token)
            ->first();

        if ($account) {
            $this->isNewUser = false;
            return $account->user;
        } else {
            $account = new SocialAccount([
                'provider_user_id' => $token,
                'provider' => $provider
            ]);

            if ($phone && $phone != null) {
                $user = User::where('phone', $phone)->first();
            }

            if (!$user && $email && $email != null) {
                $user = User::where('email', $email)->first();
            }

            if (!$user) {
                $user = new User();
                $user->email = $email;
                $user->phone = $phone;
                $user->first_name = $first_name;
                $user->second_name = $last_name;
                $user->password = bcrypt(str_random(30));
                $user->token = bcrypt($token);
                $user->save();
            }

            $account->user()->associate($user);
            $account->save();

            $this->isNewUser = true;
            return $user;
        }

    }

    public function profile(Request $request)
    {
        try {
            $user = User::where('token', '=', $request->header('x-auth-token'))->first();
//            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Користувача не існує!'], 404);
            }

            $result = array();
            $result = array_add($result, 'id', $user->id);
            $result = array_add($result, 'first_name', $user->first_name);
            $result = array_add($result, 'second_name', $user->second_name);
            $result = array_add($result, 'avatar', $user->avatar);
            $result = array_add($result, 'email', $user->email);
            $result = array_add($result, 'phone', $user->phone);

        } catch (\Exception $exception) {
            Log::warning('UsersController@profile Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return response(["data" => $result], 200);
    }

    public function history_orders(Request $request)
    {
        try {
            $user = User::where('token', '=', $request->header('x-auth-token'))->first();

            if (!$user) {
                return response()->json(['message' => 'Користувача не існує!'], 404);
            }

            $histories = HistoryBeer::select('name', 'price', 'amount', 'table', 'time')->where('user_id', $user->id)->get();

            if (!$histories) {
                return response()->json(['message' => 'Заказiв не існує!'], 404);
            }

            return response(["data" => $histories], 200);

        } catch (\Exception $exception) {
            Log::warning('UsersController@history_orders Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function menu()
    {
        try {
            $widgets = Widget::select('id', 'name', 'image')->get();

            $widgets = $widgets->each(function ($item, $key) {
                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }
            });

            return response(['data' => $widgets]);

        } catch (\Exception $exception) {
            Log::warning('UsersController@menu Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }

    public function delivery(Request $request)
    {
        try {
            $delivery = Setting::select('id', 'key', 'value')->where('key', '=', 'delivery_possible')->first();

            $delivery_possible = $delivery->value;

            return response(['data' => $delivery_possible]);

        } catch (\Exception $exception) {
            Log::warning('UsersController@delivery Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }
}
