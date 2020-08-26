<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/students",
     *     summary="Get list of students",
     *     tags={"Students"},
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/Student")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="empty resource list"
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     */
    public function index()
    {
        $students = Student::all();

        if (! empty($students) && count($students) > 0)               // some has been found
        {
            return response()->json(
                [
                    'description' => 'successful operation',
                    'data' => $students
                ], 200);
        }
        else                                                          // empty list
        {
            return response()->json(
                [
                    'description' => 'empty resource list'
                ], 204);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * @SWG\Get(
     *     path="/students/{student_id}",
     *     summary="Get student by id",
     *     tags={"Students"},
     *     description="Get student by id",
     *     @SWG\Parameter(
     *         name="student_id",
     *         in="path",
     *         description="Student id",
     *         required=true,
     *         type="integer",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *         @SWG\Schema(ref="#/definitions/Student"),
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Student is not found",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     )
     * )
     */
    public function show($id)
    {
        $student = Student::find($id);

        if (! empty($student))                                      // some has been found
        {
            return response()->json(
                [
                    'description' => 'successful operation',
                    'data' => $student
                ], 200);
        }
        else                                                        // not exist
        {
            return response()->json(
                [
                    'description' => 'Student is not found'
                ], 404);
        }

        return response()->json($student, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
