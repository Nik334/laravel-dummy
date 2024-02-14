<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB;
use App\Repositories\RoleRepository;


class RoleController extends Controller
{
    protected  $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    
    public function createRole(Request $request)
    {
        
        $roleName =$request->input('roleName');
        $userID =$request->input('userID');

    
       $response = $this->roleRepository->add($roleName,$userID);

       if ($response){
            return response()->json($response, 201);
        }else{
            return response()->json(["error"=>true,
            "message"=> "some went wront"],400);
        }
        
    }
}
