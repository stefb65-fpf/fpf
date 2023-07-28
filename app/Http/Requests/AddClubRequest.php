<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddClubRequest extends FormRequest
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
            'nomClub'   => 'string|required|max:100',
            'libelle1Club' => 'nullable|string',
            'libelle2Club' => 'nullable|string',
            'codepostalClub'=>'string|required',
            'villeClub'=>'string|required|max:50',
            'emailClub' => 'email|required',
            'nomContact' => 'string|required|max:60',
            'prenomContact' => 'string|required|max:60',
            'libelle1Contact' => 'nullable|string',
            'libelle2Contact' => 'nullable|string',
            'codepostalContact'=>'string|required',
            'villeContact'=>'string|required|max:50',
            'emailContact' => 'email|required',
            'phoneMobileContact' => 'required',
        ];
    }
}
