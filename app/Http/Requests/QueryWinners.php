<?php

namespace App\Http\Requests;

use Iamfarhad\Validation\Rules\Mobile;
use Illuminate\Foundation\Http\FormRequest;

class QueryWinners extends FormRequest
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
        // not checking existence of the values here in db because it'd be redundant in controller
        return [
            'cell_number' => ['required', new Mobile()],
            'code' => 'required|string',
        ];
    }
}
