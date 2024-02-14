<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use DateTime;
use DateTimeZone;
use PDOException;

class DepartmentController extends Controller
{
    /**
     * Create a new department.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createDepartment(Request $request)
    {
        $userID = $request->input('userID');
        $departmentName = $request->input('departmentName');

        $response = [
            'error' => false,
            'message' => 'Department created',
        ];

        // Check if the user exists
        // $isExist = $this->isUserIDExists($userID);

        // if ($isExist) {
        try {
            // Check if the department already exists
            $departmentResult = DB::table('department')
                ->where('department_name', $departmentName)
                ->get();

            if ($departmentResult->isNotEmpty()) {
                $response['message'] = 'Department already present';
                $response['error'] = true;
            } else {
                // Insert the new department
                DB::table('department')->insert([
                    'department_name' => $departmentName,
                    'status' => 'ACTIVE',
                    'added_by' => $userID,
                    'added_on' => now(),
                ]);
            }
        } catch (PDOException $e) {
            $response['message'] = 'Failed to create department' . $e;
            $response['error'] = true;
            DB::rollback();
        }
        // } else {
        //     $response['error'] = true;
        //     $response['message'] = 'Error: Invalid User';
        // }

        return response()->json($response, 201);
    }


    public function updateDepartment(Request $request)
    {
        $userID = $request->input('userID');
        $departmentName = $request->input('departmentName');
        $departmentID = $request->input('departmentID');
        $status  = $request->input('status');
        $response = ['error'=> false,'message'=>"Updated successfully"];
        try {
            // Check if the department already exists
            $departmentResult = DB::table('department')
                ->where('department_name', $departmentName)->where('id', '!=', $departmentID)
                ->get();

            if ($departmentResult->isNotEmpty()) {
                $response['message'] = 'Department already present';
                $response['error'] = true;
            } else {
                DB::table('department')->where('id', '=', $departmentID)->update([
                    'department_name' => $departmentName,
                    'status' => $status,
                ]);
            }
        } catch (PDOException $e) {
            $response['message'] = 'Failed to create department' . $e;
            $response['error'] = true;
            DB::rollback();
        }

        return response()->json($response, 200);
    }

    public function getDepartment(Request $request){
        $userID = $request->input('');
        
    }
}
