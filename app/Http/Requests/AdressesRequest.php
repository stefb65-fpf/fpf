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
            'libelle1' => 'nullable|string',
            'libelle2' => 'nullable|string',
            'libelle3' => 'nullable|string',
            'codepostal'=>'string|required|max:10',
            'ville'=>'string',
            'pays'=>'string|required',
            'telephonedomicile'=>'nullable|string|max:25',
            'telephonemobile'=>'nullable|string|max:25',
        ];
    }
}
