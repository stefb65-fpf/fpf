<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OpenRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'nom' => 'string|required|max:60',
            'prenom' => 'string|required|max:60',
            'email'=>'email|required|max:255',
            'phone_mobile'=>'required|max:25',
            'urs_id'    => 'required',
        ];
    }
}
