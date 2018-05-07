<?php

namespace App\Http\Controllers;

use App\Spectator;
use App\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class NotificationsController extends Controller
{
    public function index(Request $request)
    {
        try {
            $dt = Carbon::now();

            $notifications = Notification::select('id', 'title', 'description', 'image', 'type')
                ->where('date_start','<=',$dt)
                ->where('date_end','>=',$dt)
                ->published()
                ->get();

            $notifications = $notifications->each(function ($item, $key) {

                if ($item['image']) {
                    $source = "http://beerzha.com.ua/storage/";
                    $item['image'] = $source . $item['image'];
                }

                $source = "http://beerzha.com.ua/notification/" . $item['id'];
                $item['url'] = $source;
            });

        } catch (\Exception $exception) {
            Log::warning('NotificationsController@index Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }

        return ['data' => $notifications];
    }


    public function notificationPage(Notification $notification)
    {
        return view('notification', compact('notification'));
    }

    public function feedback(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|int|exists:notifications,id',
            'result' => [
                'required',
                'string',
                Rule::in(['opened', 'canceled'])
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Дані в запиті не заповнені або не вірні!'], 400);
        }

        try {
            $notification = Notification::findOrFail($request->id);

            $notification->total_views = $notification->total_views + 1;

            if ($request->result == "opened"){
                $notification->total_open = $notification->total_open + 1;
            }

            if ($request->result == "canceled"){
                $notification->total_canceled = $notification->total_canceled + 1;
            }

            $notification->save();

            return response()->json(['message' => 'Дані оброблено!'], 200);

        } catch (\Exception $exception) {
            Log::warning('NotificationsController@feedback Exception: ' . $exception->getMessage());
            Spectator::store(url()->current(), $exception->getMessage(), $exception->getLine());
            return response()->json(['message' => 'Упс! Щось пішло не так!'], 500);
        }
    }
}
