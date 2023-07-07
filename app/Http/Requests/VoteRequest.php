<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VoteRequest extends FormRequest
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
            'nom'   => 'required',
            'type'  => 'required',
            'debut' => 'date|nullable',
            'fin' => 'date|nullable',
            'debut_phase1' => 'date|nullable',
            'fin_phase1' => 'date|nullable',
            'debut_phase2' => 'date|nullable',
            'fin_phase2' => 'date|nullable',
            'debut_phase3' => 'date|nullable',
            'fin_phase3' => 'date|nullable',
        ];
    }
}
