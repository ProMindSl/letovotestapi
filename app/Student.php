<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @SWG\Definition(
 *  definition="Student",
 *  @SWG\Property(
 *      property="id",
 *      type="integer"
 *  ),
 *  @SWG\Property(
 *      property="last_name",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="name",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="patronymic",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="email",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="phone_number",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="country",
 *      type="string"
 *  ),
 *   @SWG\Property(
 *      property="postal_code",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="region",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="area",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="city",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="street",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="house",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="block",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="flat",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="geo_lat",
 *      type="string"
 *  ),
 *  @SWG\Property(
 *      property="geo_lon",
 *      type="string"
 *  )
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
        'geo_lon'
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
