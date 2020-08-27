<?php

namespace App\Http\Requests;

class StudentUpdateRequest extends ApiRequest
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
            'fio' => 'sometimes|required|string|max:150',
            'email' => 'sometimes|required|email|max:100|unique:students,email',
            'phone_number' => 'sometimes|required|string|max:50',
            'address' => 'sometimes|required|string|max:250'
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
            'fio.string' => 'Поле "ФИО" должно быть в формате string!',
            'email.email' => 'Поле "Адрес электронной почты" должно быть в формате string (email)!',
            'phone_number.string' => 'Поле "Телефон" должно быть в формате string!',
            'address.string' => 'Поле "Адрес проживания" должно быть в формате string!'
        ];
    }
}
