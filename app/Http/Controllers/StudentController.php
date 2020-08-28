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
     * @OA\Get
     * (
     *     path="/api/students",
     *     summary="Get list of students",
     *     tags={"Students"},
     *     @OA\Response
     *     (
     *         response=200,
     *         description="Successful get students list",
     *         @OA\JsonContent
     *         (
     *             @OA\Items(ref="#/components/schemas/Student")
     *         )
     *
     *     ),
     *     @OA\Response
     *     (
     *         response=204,
     *         description="Empty students list",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="error", type="string")
     *         )
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
     * @OA\Post
     * (
     *     path="/api/students",
     *     summary="Create student",
     *     tags={"Students"},
     *     description="Store a newly created student",
     *
     *     @OA\Parameter
     *     (
     *          name="fio",
     *          in="query",
     *          description="Student's FIO",
     *          required=true,
     *          @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="email",
     *         in="query",
     *         description="Student's e-mail",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="phone_number",
     *         in="query",
     *         description="Student's phone",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="address",
     *         in="query",
     *         description="Student's address",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response
     *     (
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent
     *         (
     *              @OA\Property( property="description", type="string"),
     *              @OA\Property( property="new_obj", type="object", allOf = { @OA\Schema(ref="#/components/schemas/Student") } )
     *         )
     *     ),
     *     @OA\Response
     *     (
     *          response="422",
     *          description="One of the parameters is incorrect",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="error_field", type="string")
     *          )
     *     ),
     *     @OA\Response
     *     (
     *          response="400",
     *          description="Unexpected server error or error accessing related third-party services",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="description", type="string")
     *          )
     *     ),
     *     @OA\Response
     *     (
     *          response="401",
     *          description="Unauthorized user",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="error", type="string")
     *          )
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
                    'description' => 'Ошибка добавления ученика: Некорректное ФИО',
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
                    'description' => 'Ошибка добавления ученика: Некорректный номер телефона',
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
                    'description' => 'Ошибка добавления ученика: Указанный номер телефона уже существует в базе данных',
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
                    'description' => 'Ошибка добавления ученика: Некорректный адрес',
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
                        'description' => 'Непредвиденная ошибка добавления ученика'
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
     * @OA\Get
     * (
     *     path="/api/students/{student_id}",
     *     summary="Get student by id",
     *     tags={"Students"},
     *     description="Get student by id",
     *     @OA\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response
     *     (
     *         response=200,
     *         description="Successful get student",
     *         @OA\JsonContent(ref="#/components/schemas/Student"),
     *     ),
     *     @OA\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @OA\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="error", type="string")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $student = Student::findOrFail($id);
        return response()->json($student, 200);
    }

    /**
     * @OA\Put
     * (
     *     path="/api/students/{student_id}",
     *     summary="Update student by id",
     *     tags={"Students"},
     *     description="Update student by id",
     *     @OA\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="fio",
     *         in="query",
     *         description="Student's FIO",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="email",
     *         in="query",
     *         description="Student's e-mail",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="phone_number",
     *         in="query",
     *         description="Student's phone",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter
     *     (
     *         name="address",
     *         in="query",
     *         description="Student's address",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response
     *     (
     *         response=200,
     *         description="Successful update student",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="upd_obj", type="object", allOf = { @OA\Schema(ref="#/components/schemas/Student") } )
     *         )
     *     ),
     *     @OA\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @OA\Response
     *     (
     *          response="422",
     *          description="One of the parameters is incorrect",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="description", type="string"),
     *              @OA\Property(property="error_field", type="string")
     *          )
     *     ),
     *     @OA\Response
     *     (
     *          response="400",
     *          description="Unexpected server error or related services",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="description", type="string")
     *          )
     *     ),
     *     @OA\Response
     *     (
     *          response="401",
     *          description="Unauthorized user",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="error", type="string")
     *          )
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
     * @OA\Delete
     * (
     *     path="/api/students/{student_id}",
     *     summary="Remove student by id",
     *     tags={"Students"},
     *     description="Remove student from storage by id",
     *     @OA\Parameter
     *     (
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response
     *     (
     *         response=200,
     *         description="Successful delete student",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="description", type="string")
     *         )
     *     ),
     *     @OA\Response
     *     (
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @OA\Response(
     *          response="400",
     *          description="Unexpected server error or related services",
     *          @OA\JsonContent
     *          (
     *              @OA\Property(property="description", type="string")
     *          )
     *     ),
     *     @OA\Response
     *     (
     *         response="401",
     *         description="Unauthorized user",
     *         @OA\JsonContent
     *         (
     *              @OA\Property(property="error", type="string")
     *         )
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
