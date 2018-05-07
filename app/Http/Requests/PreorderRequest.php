<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PreorderRequest extends FormRequest
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
            'time' => ['required', 'regex:^(([0-1][0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?)$^'],
            'date' => 'required|date',
            'count_people' => 'required|int',
            'callback' => 'required|string|in:YES,NO',
        ];
    }
}
