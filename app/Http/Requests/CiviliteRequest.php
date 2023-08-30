<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CiviliteRequest extends FormRequest
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
            'nom' => ['string','required','max:40', 'min:2'],
            'prenom' => ['string','required','max:40', 'min:2'],
            'datenaissance'=>'date',
            'phone_mobile'=>'nullable|string|max:25',
        ];
    }
}
