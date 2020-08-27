<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentCreateRequest;
use App\Http\Requests\StudentDeleteRequest;
use App\Http\Requests\StudentUpdateRequest;
use App\Student;
use Dadata\DadataClient;
use \Validator;

class StudentController extends Controller
{
    /**
     * @SWG\Get
     * (
     *     path="/students",
     *     summary="Get list of students",
     *     tags={"Students"},
     *     @SWG\Response
     *     (
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema
     *         (
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Student")
     *         ),
     *     ),
     *     @SWG\Response
     *     (
     *         response=204,
     *         description="empty resource list"
     *     ),
     *     @SWG\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     */
    public function index()
    {
        $students = Student::all();

        if (! empty($students) && count($students) > 0)               // resources has been found
        {
            return response()->json($students, 200);
        }
        else                                                          // empty list
        {
            return response()->json(
                [
                    'description' => 'Список учеников пуст'
                ], 204);
        }
    }

    /**
     * @SWG\Post
     * (
     *     path="/students",
     *     summary="Store a newly created student",
     *     tags={"Students"},
     *     description="Store a newly created student",
     *     @SWG\Parameter
     *     (
     *         name="fio",
     *         in="body",
     *         description="Student FIO",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="email",
     *         in="body",
     *         description="Student e-mail",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="phone_number",
     *         in="body",
     *         description="Student phone number",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="address",
     *         in="body",
     *         description="Student's address",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response
     *     (
     *         response=201,
     *         description="successful operation",
     *         @SWG\Schema
     *         (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="new_obj",
     *                      @SWG\Schema(ref="#/definitions/Student")
     *                  ),
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="422",
     *          description="One of the parameters is incorrect",
     *          @SWG\Schema
     *          (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="error_field",
     *                      type="string"
     *                  ),
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="400",
     *          description="Unexpected server error or related services",
     *          @SWG\Schema
     *          (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response
     *     (
     *          response="401",
     *          description="Unauthorized user",
     *     )
     * )
     */
    public function store(StudentCreateRequest $request)
    {
        // input params
        $inputFio = $request->input('fio');
        $inputEmail = $request->input('email');
        $inputPhone = $request->input('phone_number');
        $inputAddress = $request->input('address');

                                                                                // validate fio
        $dataFioList = self::getCorrectDataList('name', $inputFio);
        if ( $dataFioList == null ) return self::throwDaDataAccsessError();
        $successCodeFio = (int)$dataFioList['qc'];

        if (    $successCodeFio == config('dadata.code_error')
            ||  $successCodeFio == config('dadata.code_halfsuccess')
            ||  $successCodeFio == config('dadata.code_ambiguous') )
        {
            return response()->json(
                [
                    'description' => 'Ошибка добаления ученика: Некорректное ФИО',
                    'error_field' => 'fio'
                ], 422);
        }
                                                                                // validate phone

        $dataPhoneList = self::getCorrectDataList('phone', $inputPhone);
        if ( $dataPhoneList == null ) return self::throwDaDataAccsessError();
        $successCodePhone = (int)$dataPhoneList['qc'];

        if (    $successCodePhone == config('dadata.code_error')
            ||  $successCodePhone == config('dadata.code_halfsuccess')
            ||  $successCodePhone == config('dadata.code_ambiguous') )
        {
            return response()->json(
                [
                    'description' => 'Ошибка добаления ученика: Некорректный номер телефона',
                    'error_field' => 'phone_number'
                ], 422);
        }

        $validator = Validator::make(
            ['phone_number' => $dataPhoneList['phone']],
            ['phone_number' => ['max:50', 'unique:students,phone_number']]
        );

        if ( $validator->fails() )
        {
            return response()->json(
                [
                    'description' => 'Ошибка добаления ученика: Указанный номер телефона уже существует в базе данных',
                    'error_field' => 'phone_number'
                ], 422);
        }

                                                                                // validate address

        $dataAddressList = self::getCorrectDataList('address', $inputAddress);
        if ( $dataAddressList == null ) return self::throwDaDataAccsessError();
        $successCodeAddress = (int)$dataAddressList['qc'];

        if (    $successCodeAddress == config('dadata.code_error')
            ||  $successCodeAddress == config('dadata.code_halfsuccess') )
        {
            return response()->json(
                [
                    'description' => 'Ошибка добаления ученика: Некорректный адрес',
                    'error_field' => 'address'
                ], 422);
        }

        try
        {
            $newStudent = new Student();
            $newStudent->last_name = $dataFioList['surname'];
            $newStudent->name = $dataFioList['name'];
            $newStudent->patronymic = $dataFioList['patronymic'];
            $newStudent->email = $inputEmail;
            $newStudent->phone_number = $dataPhoneList['phone'];
            $newStudent->full_address = $dataAddressList['result'];
            $newStudent->country = $dataAddressList['country'];
            $newStudent->postal_code = $dataAddressList['postal_code'];
            $newStudent->region = $dataAddressList['region'];
            $newStudent->area = $dataAddressList['area'];
            $newStudent->city_type = $dataAddressList['city_type'];
            $newStudent->city = $dataAddressList['city'];
            $newStudent->street = $dataAddressList['street'];
            $newStudent->house = $dataAddressList['house'];
            $newStudent->block = $dataAddressList['block'];
            $newStudent->flat = $dataAddressList['flat'];
            $newStudent->qc_address = $successCodeAddress;
            $newStudent->geo_lat = $dataAddressList['geo_lat'];
            $newStudent->geo_lon = $dataAddressList['geo_lon'];

            $newStudent->save();

            $newId = $newStudent->id;

            if($newId)                                                  // if success added
            {
                return response()->json(
                    [
                        'description' => 'Ученик добавлен',
                        'new_obj' => $newStudent
                    ], 201);

            }
            else                                                        // if false added
            {
                return response()->json(
                    [
                        'description' => 'Непредвиденная ошибка добаления ученика'
                    ], 400);
            }
        }
        catch(\Exception $e)                                             // unexpected error
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
        catch (\Throwable $t)
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
    }

    /**
     * @SWG\Get
     * (
     *     path="/students/{student_id}",
     *     summary="Get student by id",
     *     tags={"Students"},
     *     description="Get student by id",
     *     @SWG\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response
     *     (
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(ref="#/definitions/Student"),
     *     ),
     *     @SWG\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @SWG\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *     )
     * )
     */
    public function show($id)
    {
        $student = Student::find($id);

        if (! empty($student))                                      // student has been found
        {
            return response()->json($student, 200);
        }
        else                                                        // student not exist
        {
            return response()->json(
                [
                    'description' => 'Student is not found'
                ], 404);
        }
    }

    /**
     * @SWG\Put
     * (
     *     path="/students/{student_id}",
     *     summary="Update the student by id",
     *     tags={"Students"},
     *     description="Update the student by id",
     *     @SWG\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="fio",
     *         in="body",
     *         description="Student FIO",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="email",
     *         in="body",
     *         description="Student e-mail",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="phone_number",
     *         in="body",
     *         description="Student phone number",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Parameter
     *     (
     *         name="address",
     *         in="body",
     *         description="Student's address",
     *         required=false,
     *         type="string",
     *     ),
     *     @SWG\Response
     *     (
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema
     *         (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="upd_obj",
     *                      @SWG\Schema(ref="#/definitions/Student")
     *                  ),
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @SWG\Response(
     *          response="422",
     *          description="One of the parameters is incorrect",
     *          @SWG\Schema
     *          (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="error_field",
     *                      type="string"
     *                  ),
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response(
     *          response="400",
     *          description="Unexpected server error or related services",
     *          @SWG\Schema
     *          (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response
     *     (
     *          response="401",
     *          description="Unauthorized user",
     *     )
     * )
     */
    public function update(StudentUpdateRequest $request, $id)
    {

        $studentForUpd = Student::where('id', $id)->firstOrFail();

        try
        {
                                                                                        // validate and update fio

            if ( $request->has('fio') )
            {
                $dataFioList = self::getCorrectDataList('name', $request->input('fio'));
                if ( $dataFioList == null ) return self::throwDaDataAccsessError();
                $successCodeFio = (int)$dataFioList['qc'];

                if (    $successCodeFio == config('dadata.code_error')
                    ||  $successCodeFio == config('dadata.code_halfsuccess')
                    ||  $successCodeFio == config('dadata.code_ambiguous') )
                {
                    return response()->json(
                        [
                            'description' => 'Ошибка обновления данных ученика: Некорректное ФИО',
                            'error_field' => 'fio'
                        ], 422);
                }

                $studentForUpd->last_name = $dataFioList['surname'];
                $studentForUpd->name = $dataFioList['name'];
                $studentForUpd->patronymic = $dataFioList['patronymic'];
            }
                                                                            // update email (validation was in request class)
            if ( $request->has('email') )
            {
                $studentForUpd->email = $request->input('email');
            }
                                                                            // validate and update phone
            if ( $request->has('phone_number') )
            {
                $dataPhoneList = self::getCorrectDataList('phone', $request->input('phone_number'));
                if ( $dataPhoneList == null ) return self::throwDaDataAccsessError();
                $successCodePhone = (int)$dataPhoneList['qc'];

                if (    $successCodePhone == config('dadata.code_error')
                    ||  $successCodePhone == config('dadata.code_halfsuccess')
                    ||  $successCodePhone == config('dadata.code_ambiguous') )
                {
                    return response()->json(
                        [
                            'description' => 'Ошибка обновления данных ученика: Некорректный номер телефона',
                            'error_field' => 'phone_number'
                        ], 422);
                }

                $validator = Validator::make(
                    ['phone_number' => $dataPhoneList['phone']],
                    ['phone_number' => ['max:50', 'unique:students,phone_number']]
                );

                if ( $validator->fails() )
                {
                    return response()->json(
                        [
                            'description' => 'Ошибка обновления данных ученика: Указанный номер телефона уже существует в базе данных',
                            'error_field' => 'phone_number'
                        ], 422);
                }

                $studentForUpd->phone_number = $dataPhoneList['phone'];
            }
                                                                        // validate and update address
            if ( $request->has('address') )
            {
                $dataAddressList = self::getCorrectDataList('address',  $request->input('address'));
                if ( $dataAddressList == null ) return self::throwDaDataAccsessError();
                $successCodeAddress = (int)$dataAddressList['qc'];

                if (    $successCodeAddress == config('dadata.code_error')
                    ||  $successCodeAddress == config('dadata.code_halfsuccess') )
                {
                    return response()->json(
                        [
                            'description' => 'Ошибка обновления данных ученика: Некорректный адрес',
                            'error_field' => 'address'
                        ], 422);
                }

                $studentForUpd->full_address = $dataAddressList['result'];
                $studentForUpd->country = $dataAddressList['country'];
                $studentForUpd->postal_code = $dataAddressList['postal_code'];
                $studentForUpd->region = $dataAddressList['region'];
                $studentForUpd->area = $dataAddressList['area'];
                $studentForUpd->city_type = $dataAddressList['city_type'];
                $studentForUpd->city = $dataAddressList['city'];
                $studentForUpd->street = $dataAddressList['street'];
                $studentForUpd->house = $dataAddressList['house'];
                $studentForUpd->block = $dataAddressList['block'];
                $studentForUpd->flat = $dataAddressList['flat'];
                $studentForUpd->qc_address = $successCodeAddress;
                $studentForUpd->geo_lat = $dataAddressList['geo_lat'];
                $studentForUpd->geo_lon = $dataAddressList['geo_lon'];
            }
                                                                            // try save update
            $isUpdated = $studentForUpd->save();

            if($isUpdated)                                                  // if success update
            {
                return response()->json(
                    [
                        'description' => 'Данные ученика успешно обновлены',
                        'upd_obj' => $studentForUpd
                    ], 200);
            }
            else                                                            // if false update
            {
                return response()->json(
                    [
                        'description' => 'Непредвиденная ошибка обновления данных ученика'
                    ], 400);
            }
        }
        catch(\Exception $e)                                                // unexpected error
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
        catch (\Throwable $t)
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
    }

    /**
     * @SWG\Delete
     * (
     *     path="/students/{student_id}",
     *     summary="Remove student from storage by id",
     *     tags={"Students"},
     *     description="Remove student from storage by id",
     *     @SWG\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response
     *     (
     *         response=200,
     *         description="successful operation"
     *     ),
     *     @SWG\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @SWG\Response(
     *          response="400",
     *          description="Unexpected server error or related services",
     *          @SWG\Schema
     *          (
     *              type="array",
     *              @SWG\Items
     *              (
     *                  type="object",
     *                  @SWG\Property
     *                  (
     *                      property="description",
     *                      type="string"
     *                  )
     *              )
     *          )
     *     ),
     *     @SWG\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *     )
     * )
     */
    public function destroy(StudentDeleteRequest $request, $id)
    {
        $studentForDel = Student::findOrFail($id);

        try
        {
            $studentForDel->delete();

            return response()->json(
                [
                    'description' => 'Ученик успешно удален'
                ], 200);

        }
        catch(\Exception $e)                                                // unexpected error
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
        catch (\Throwable $t)
        {
            return response()->json(
                [
                    'description' => 'Непредвиденная ошибка на сервере'
                ], 400);
        }
    }

    /**
     * Get corrected data list.
     *
     * @param  string  $dataType
     * @param  string  $inputSource
     * @return array
     */
    private function getCorrectDataList($dataType, $inputSource)
    {
        $token = env('DADATA_TOKEN');
        $secret = env('DADATA_SECRET');
        try
        {
            $dadata = new DadataClient($token, $secret);
            return $dadata->clean($dataType, $inputSource);
        }
        catch(\Exception $e)                                             // unexpected error
        {
            return null;
        }
        catch (\Throwable $t)
        {
            return null;
        }
    }

    /**
     * Throw error response.
     *
     * @return \Illuminate\Http\Response
     */
    private function throwDaDataAccsessError()
    {
        return response()->json(
            [
                'description' => 'Ошибка на сервере: сторонний сервис валидации недоступен! Попробуйте повторить запрос позже.'
            ], 400);
    }
}
