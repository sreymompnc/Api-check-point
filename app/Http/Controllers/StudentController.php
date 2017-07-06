<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Students;
use Illuminate\Support\Facades\Validator; // to use validator
use Illuminate\Foundation\Validation; // to use validation message


class StudentController extends Controller
{

    public function __construct()
    {
        $this->middleware('key'); // call middleware name key that set in Kernel.php
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * This method is used to select all students information
     * @return \Illuminate\Http\JsonResponse
     */
    public function view()
    {
        $studentsData = \DB::table('students')->get();
        return response()->json(array('status' => 'True', 'students' => $studentsData));

    }

    /**
     * This method is use to view profile student specific by ID
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function profile($id)
    {
        $studentProfile = \DB::table('students')->select('*')->where('id', '=', $id)->get();
        return response()->json(array('status' => 'True', 'student profile' => $studentProfile));

    }

    /**
     * register a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => 'required|email|unique:students',
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:5',

        ]);


        if ($validator->fails()) {
            return response()->json(array(
                'status' => 'False',
                'message' => "Student's registration is fail!",
                'validation' => $validator->errors()
            ));
        } else {

            $student = new Students();

            $student->username = $request->input('username');
            $student->email = $request->input('email');
            $student->phone = $request->input('phone');
            $student->address = $request->input('address');
            $student->status = '1';
            $student->password = sha1($request->input('password'));

            $student->save();

            return response()->json(array('status' => 'True', 'message' => 'Student is registered successfully'));

        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => "required|email|unique:students,Email,$id",
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:5',

        ]);


        if ($validator->fails()) {
            return response()->json(array(
                'status' => 'False',
                'message' => "Student's updating is fail!",
                'validation' => $validator->errors()
            ));
        } else {

            $student = Students::find($id);

            $student->username = $request->input('username');
            $student->email = $request->input('email');
            $student->phone = $request->input('phonne');
            $student->address = $request->input('address');
            $student->status = $request->input('status');
            $student->password = $request->input('password');
            $student->save();

            return response()->json(array('status' => 'True', 'message' => 'Student is updated successfully', 'New student data' => $request->all()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $result = \DB::table('students')->where('id', '=', $id)->delete();
        if ($result) {
            return response()->json(array('status' => 'True', 'message' => 'Student is deleted successfully'));
        } else {
            return response()->json(array('status' => 'False', 'message' => "Student's deleting is deleted successfully"));
        }
    }

    /**
     * This method is used to search student base on their username, address and status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $username = $request->input('username');
        $add = $request->input('address');
        $status = $request->input('status');

        $result = \DB::table('students')->where([
            ['username', '=', $username],
            ['address', '=', $add],
            ['status', '=', $status],
        ])->get();

        if ($result) {
            return response()->json(array('status' => 'True', 'result of searching' => $result));
        } else {
            return response()->json(array('status' => 'False', 'result of searching' => 'Not match'));
        }
    }

    /**
     * Login method
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => "required|email",
            'password' => 'required',
        ]);

        // To validate login
        if ($validator->passes()) {
            $student = \DB::table('students')->select('*')->where([
                ['email', '=', $request->input('email')],
                ['password', '=', sha1($request->input('password'))],
            ])->get();
            // To check if login is success or fail
            if ($student) {
                return response()->json(array(
                    'status' => 'True',
                    'message' => 'Login successfully',
                    "Student's information" => $student
                ));
            } else {
                return response()->json(array('status' => 'False', 'message' => 'Login fail!'));
            }
        } else {
            return response()->json(array('status' => 'False', 'validation' => $validator->errors()));
        }
    }

}
