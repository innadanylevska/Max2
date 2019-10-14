<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VacancyCreateRequest extends FormRequest
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
            'vacancy_name' => 'required|string|max:100',
            'workers_amount' => 'required|numeric',
            'salary' => 'required|numeric',
            'organization_id' => 'required|numeric',
        ];
    }
}
