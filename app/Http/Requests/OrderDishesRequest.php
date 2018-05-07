<?php

namespace App\Http\Requests;

use GuzzleHttp\Psr7\Request;
use Illuminate\Foundation\Http\FormRequest;

class OrderDishesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'orders' => 'required',
            'orders.*.id' => 'required|int',
            'orders.*.amount' => 'required|int',
            'payment' => 'required|string|in:cash,card',
            'table' => 'required|int',
            'code' => 'required|int',
        ];
    }
}
