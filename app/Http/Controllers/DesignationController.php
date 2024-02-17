<?php

namespace App\Http\Controllers;

use App\Repositories\DesignationRepository;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    protected  $designationRepository;

    public function __construct(DesignationRepository $designationRepository)
    {
        $this->designationRepository = $designationRepository;
    }

    public function createDesignation(Request $request)
    {

        $designationName = $request->input('designationName');
        $departmentID = $request->input('departmentID');
        $userID = $request->input('userID');


        $response = $this->designationRepository->add($designationName,$departmentID,$userID);

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

    public function getDesignation(Request $request){
        $departmentID = $request->input("departmentID");
        $status = $request->input("status");
        $generalSearch = $request->input("generalSearch");
        $sortOrder = $request->input("sortOrder");
        $iDisplayStart = $request->input("iDisplayStart");
        $iDisplayLength = $request->input("iDisplayLength");

        $response = [
            "error"=>false,
            "data" =>[],
            "totalCount" =>0,
            "message"=>"Designation"
        ];

        $return = $this->designationRepository->get($departmentID,$status, $generalSearch,$iDisplayStart,$iDisplayLength,$sortOrder);
        if ($return["error"]==false) {
            $response = [
                "message"=>"Designation",
                "error"=>false,
                "data" =>$return["data"],
                "totalCount" => $return["totalCount"],
                "query" =>$return["query"]
            ];
        } else {
            $response = [
                "error"=>true,
                "data" =>"No Data Found",
                
            ];
        }

        return response()->json($response, 201);

    }

    public function updateRole(Request $request)
    {

        $roleName = $request->input('roleName');
        $roleID = $request->input('roleID');
        $status = $request->input('status');


        $response = $this->designationRepository->updated($roleID,$roleName, $status);

        if ($response) {
            return response()->json($response, 201);
        } else {
            return response()->json(
                [
                    "error" => true,
                    "message" => "some went wront"
                ],
                400
            );
        }
    }
}
