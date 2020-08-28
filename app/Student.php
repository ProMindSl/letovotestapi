<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema
 * (
 *  title="Student",
 *  @OA\Property
 *  (
 *      property="id",
 *      type="integer",
 *      description="Id ученика"
 *  ),
 *  @OA\Property
 *  (
 *      property="last_name",
 *      type="string",
 *      description="Фамилия"
 *  ),
 *  @OA\Property
 *  (
 *      property="name",
 *      type="string",
 *      description="Имя"
 *  ),
 *  @OA\Property
 *  (
 *      property="patronymic",
 *      type="string",
 *      description="Отчество"
 *  ),
 *  @OA\Property
 *  (
 *      property="email",
 *      type="string",
 *      description="Email адрес"
 *  ),
 *  @OA\Property
 *  (
 *      property="phone_number",
 *      type="string",
 *      description="Номер телефона"
 *  ),
 *  @OA\Property
 *  (
 *      property="full_address",
 *      type="string",
 *      description="Полный адрес"
 *  ),
 *  @OA\Property
 *  (
 *      property="country",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Страна"
 *   ),
 *   @OA\Property
 *  (
 *      property="postal_code",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Индекс"
 *  ),
 *  @OA\Property
 *  (
 *      property="region",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Регион"
 *  ),
 *  @OA\Property
 *  (
 *      property="area",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Область"
 *  ),
 *  @OA\Property
 *  (
 *      property="city",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Город"
 *  ),
 *  @OA\Property
 *  (
 *      property="city_type",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Тип населенного пункта"
 *  ),
 *  @OA\Property
 *  (
 *      property="street",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Улица"
 *  ),
 *  @OA\Property
 *  (
 *      property="house",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Дом"
 *  ),
 *  @OA\Property
 *  (
 *      property="block",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Корпус"
 *  ),
 *  @OA\Property
 *  (
 *      property="flat",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Квартира"
 *  ),
 *  @OA\Property
 *  (
 *      property="geo_lat",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Координата широты "
 *  ),
 *  @OA\Property
 *  (
 *      property="geo_lon",
 *      type="string",
 *      description="Доп инфо по адресу проживания: Координата долготы"
 *  ),
 *  @OA\Property
 *  (
 *      property="qc_address",
 *      type="integer",
 *      description="Доп инфо по адресу проживания: Код корректности адресных данных"
 *  )
 *
 * )
 */
class Student extends Model
{
    protected $table = 'students';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'last_name',
        'name',
        'patronymic',
        'email',
        'phone_number',
        'country',
        'postal_code',
        'region',
        'area',
        'city',
        'street',
        'house',
        'block',
        'flat',
        'geo_lat',
        'geo_lon',
        'city_type',
        'qc_address',
        'full_address'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'pivot'
    ];
}
