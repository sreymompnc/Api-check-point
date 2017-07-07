<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Students;
use Illuminate\Support\Facades\Validator; // to use validator
use Illuminate\Foundation\Validation; // to use validation message
use Exception;
use Illuminate\Support\Facades\Response;


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
        if ($studentsData) {
            return response()->json(array('Status' => 'True', 'Students' => $studentsData));
        } else {
            return response()->json(array('Status' => 'False', 'Message' => 'No data in database'), 404);
        }

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
        if ($studentProfile) {
            return response()->json(array('Status' => 'True', "Student's profile" => $studentProfile));
        } else {
            return response()->json(array('Status' => 'False', 'Message' => 'Invalid id'), 404);
        }

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
                'message' => "Register fail!",
                'Error' => $validator->errors()
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

            return response()->json(array('Status' => 'True', 'Message' => 'Register successfully'));

        }


    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[\pL\s\-]+$/u',
            'email' => "required|email|unique:students,email,$id",
            'phone' => 'required',
            'address' => 'required',
            'password' => 'required|min:5',

        ]);
        // Validator is true
        if ($validator->fails()) {
            return response()->json(array(
                'Status' => 'False',
                'Message' => "Edit fail!",
                'Error' => $validator->errors()
            ));
        } else {

            $student = Students::find($id);
            if ($student) {
                $student->username = $request->input('username');
                $student->email = $request->input('email');
                $student->phone = $request->input('phone');
                $student->address = $request->input('address');
                $student->status = '1';
                $student->password = sha1($request->input('password'));
                $student->save();

                $studentNewData = \DB::table('students')->select('*')->where('id','=',$id)->get();

                return response()->json(array('Status' => 'True', 'Message' => 'Edit successfully', 'New student data' => $studentNewData));
            } else {
                return response()->json(array('Status' => 'False', 'Message' => 'Invalid id'), 404);
            }
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
        if (Students::find($id)) {
            $result = \DB::table('students')->where('id', '=', $id)->delete();
            if ($result) {
                return response()->json(array('Status' => 'True', 'Message' => 'Delete successfully'));
            } else {
                return response()->json(array('Status' => 'False', 'Message' => "Delete fail!"));
            }
        } else {
            return response()->json(array('Status' => 'False', 'Message' => "Invalid id"), 404);
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
        $address = $request->input('address');
        $status = $request->input('status');

        $result = \DB::table('students')->where([
            ['username', '=', $username],
            ['address', '=', $address],
            ['status', '=', $status],
        ])->get();

        if ($result) {
            return response()->json(array('Status' => 'True', "Student's information" => $result));
        } else {
            return response()->json(array('Status' => 'False', "Student's information" => "Information doesn't match"));
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

            if (count($student) > 0) {
                return response()->json(array(
                    'status' => 'True',
                    'message' => 'Login successfully',
                    "Student's information" => $student
                ));
            } else {
                return response()->json(array('Status' => 'False', 'Message' => 'Invalid email or password!'), 404);
            }

        } else {
            return response()->json(array('Status' => 'False', 'Message' => 'Login fail!', 'Error' => $validator->errors()));
        }
    }

}
