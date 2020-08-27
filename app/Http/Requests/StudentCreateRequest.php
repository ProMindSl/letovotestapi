<?php

namespace App\Http\Requests;

class StudentCreateRequest extends ApiRequest
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

    public function rules()
    {
        return [
            'fio' => 'required|string|max:150',
            'email' => 'required|email|max:100|unique:students,email',
            'phone_number' => 'required|string|max:50',
            'address' => 'required|string|max:250'
        ];
    }


    /**
     * Custom message for validation
     *
     * @return array
     */
    public function messages()
    {
        return [
            'fio.required' => 'ФИО - обязательное поле!',
            'email.required' => 'Адрес почты - обязательное поле!',
            'phone_number.required' => 'Телефон - обязательное поле!',
            'address.required' => 'Адрес проживания - обязательное поле!'
        ];
    }
}
