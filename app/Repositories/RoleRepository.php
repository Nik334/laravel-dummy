<?php

namespace App\Repositories;

use App\Exceptions\ServiceException;
use App\Http\StatusCodes;
use App\Models\Token;
use App\Models\User;
use App\Models\Role;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class RoleRepository
{
    private Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function add($roleName,$userID)
    {
        
        DB::beginTransaction();
        $return = [];
        $return['message'] = 'Created successfully';
        $return['error'] = false;
        try {
            

            $roleResult = DB::table('role')
                ->where('role_name', $roleName)
                ->get();

            if ($roleResult->isNotEmpty()) {
                $return['message'] = 'Role already present';
                $return['error'] = true;
            }else{
                DB::table('role')->insert([
                    'role_name' => $roleName,
                    'status' => 'ACTIVE',
                    'added_by' => $userID,
                    'added_on' => now(),
                ]);
                DB::commit();

            }
        } catch (Exception $e) {
            $return['message'] = 'Failed to create '.$e;
            $return['error'] = true;
        } finally {
            DB::rollback();
        }

        return $return;
    }


}
