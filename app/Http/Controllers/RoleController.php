<?php

namespace App\Http\Controllers;
use App\Repositories\RoleRepository;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    protected  $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    public function createRole(Request $request)
    {

        $roleName = $request->input('roleName');
        $userID = $request->input('userID');


        $response = $this->roleRepository->add($roleName, $userID);

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

    public function getRole(Request $request){
        $status = $request->input("status");
        $generalSearch = $request->input("generalSearch");
        $sortOrder = $request->input("sortOrder");
        $iDisplayStart = $request->input("iDisplayStart");
        $iDisplayLength = $request->input("iDisplayLength");

        $response = [
            "error"=>false,
            "data" =>[],
            "totalCount" =>0,
            "message"=>"Role"
        ];

        $return = $this->roleRepository->get($status, $generalSearch,$iDisplayStart,$iDisplayLength,$sortOrder);
        if ($return["error"]==false) {
            $response = [
                "message"=>"Role",
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


        $response = $this->roleRepository->updated($roleID,$roleName, $status);

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
