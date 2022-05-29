<?php

namespace App\Http\Requests;

use Iamfarhad\Validation\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class StoreWinner extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'number' => ['required', new Mobile()],
            'message' => 'required|string',
        ];
    }
}
