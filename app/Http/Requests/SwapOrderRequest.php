<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SwapOrderRequest extends FormRequest
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
            'firstTask.id' => 'required',
            'secondTask.id' => 'required',
            'firstTask.order_id' => 'required',
            'secondTask.order_id' => 'required',
        ];
    }
}
