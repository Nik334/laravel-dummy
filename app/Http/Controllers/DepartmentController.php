<?php

namespace App\Http\Controllers;

use App\Repositories\DepartmentRepository;
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

     protected $departmentRepository;
     public function __construct(DepartmentRepository $departmentRepository)
     {
         $this->departmentRepository = $departmentRepository;
     }
    public function createDepartment(Request $request)
    {
        $userID = $request->input('userID');
        $departmentName = $request->input('departmentName');

       
        $response = $this->departmentRepository->add($departmentName, $userID);

        if ($response) {
            return response()->json($response, 201);
        } else {
            return response()->json(
                [
                    "error" => true,
                    "message" => "something went wrong"
                ],
                400
            );
        }
    }

    public function getDepartment(Request $request){

        $status = $request->input("status");
        $generalSearch = $request->input('generalSearch');
        $sortOrder = $request->input("sortOrder");
        $iDisplayStart = $request->input("iDisplayStart");
        $iDisplayEnd = $request->input("iDisplayEnd");

        $response = [
            "error"=>false,
            "data" =>[],
            "totalCount" =>0,
            "message"=>"Role"
        ];

        $return = $this->departmentRepository->get($status, $generalSearch,$iDisplayStart,$iDisplayEnd,$sortOrder);
        if ($return["error"]==false) {
            $response = [
                "message"=>"Department",
                "error"=>false,
                "data" =>$return["data"],
                "totalCount" => $return["totalCount"]
            ];
        } else {
            $response = [
                "error"=>true,
                "data" =>"No Data Found",
                
            ];
        }

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

   
}
