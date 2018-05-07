<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderBeerRequest extends FormRequest
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
            'id' => 'required|int',
            'payment' => 'required|string|in:cash,card',
            'price' => 'required|numeric',
            'amount' => 'required|int',
        ];
    }
}
