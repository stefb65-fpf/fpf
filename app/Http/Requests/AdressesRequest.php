<?php

namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;

class AdressesRequest extends FormRequest
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
            'libelle1' => ['nullable','string', 'max:120'],
            'libelle2' => ['nullable','string', 'max:120'],
//            'libelle3' => 'nullable|string',
            'codepostal'=>['string', 'max:10', 'required'],
            'ville'=> ['string','max:50'],
            'pays'=>['numeric', 'required'],
            'telephonedomicile'=>'nullable|string',

        ];
    }
}
