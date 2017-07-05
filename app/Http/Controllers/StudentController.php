<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Students;
use Crypt;

class StudentController extends Controller
{

    public function __construct()
    {
      //  $this->middleware('key'); // call middleware name key that set in Kernel.php
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        echo ' hello';die();
    }

    /**
     * This method is used to select all students information
     * @return \Illuminate\Http\JsonResponse
     */
    public function view(){
        $studentsData = \DB::table('students')->get();
        return response()->json(array('status' => 'True', 'students' => $studentsData));

    }

    public function profile($id){
        $studentProfile = \DB::table('students')->select('*')->where('id', '=',$id)->get();
        return response()->json(array('status'=> 'True','student profile' => $studentProfile ));

    }

    /**
     * register a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $student = new Students();
        $encrypted = Crypt::encrypt($request->input('password'));

        $student->username = $request->input('username');
        $student->email = $request->input('email');
        $student->phone = $request->input('phone');
        $student->address = $request->input('address');
        $student->status = '1';
        $student->password = $encrypted;

        $student->save();
        return response()->json(array('status' => 'True', 'message' => 'Student is registered successfully'));




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

        $student = Students::find($id);

        $student->username = $request->input('username');
        $student->email = $request->input('email');
        $student->phone = $request->input('phonne');
        $student->address = $request->input('address');
        $student->status = $request->input('status');
        $student->password = $request->input('password');
        $student->save();

        return response()->json(array('status'=> 'True','message' => 'Student is updated successfully','New student' => $request->all() ));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        \DB::table('students')->where('id','=',$id)->delete();
        return response()->json(array('status' => 'True','message' => 'Student is deleted successfully'));
    }

    public function search(Request $request){
        $username = $request->input('username');
        $add = $request->input('address');
        $status = $request->input('status');

        $result = \DB::table('students')->where([
            ['username','=',$username],
            ['address','=',$add],
            ['status','=',$status],
        ])->get();

        return response()->json(array('status' => 'True','result of searching' =>$result ));
    }
}
