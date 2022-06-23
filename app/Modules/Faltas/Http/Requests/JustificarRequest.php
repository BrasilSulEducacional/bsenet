<?php

namespace App\Modules\Faltas\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JustificarRequest extends FormRequest
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
            'observacao' => 'required|min:20'
        ];
    }
}
