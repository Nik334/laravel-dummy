<?php

namespace App\Repositories;

use App\Models\Role;
use Exception;

use Illuminate\Support\Facades\DB;


class RoleRepository
{
    private Role $role;

    public function __construct(Role $role)
    {
        $this->role = $role;
    }

    public function add($roleName, $userID)
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
            } else {
                DB::table('role')->insert([
                    'role_name' => $roleName,
                    'status' => 'ACTIVE',
                    'added_by' => $userID,
                    'added_on' => now(),
                ]);
                DB::commit();
            }
        } catch (Exception $e) {
            $return['message'] = 'Failed to create ';
            $return['error'] = true;
        } finally {
            DB::rollback();
        }

        return $return;
    }

    public function get($status, $generalSearch, $iDisplayStart, $iDisplayLength, $sortOrder)
    {
        $roleQuery = DB::table('role as r')
            ->select('r.*', 'u.user_name as added_by_name')
            ->join('users as u', 'u.id', '=', 'r.added_by');

        if ($status !== null && $status !== "") {
            $roleQuery->where('r.status', $status);
        }

        if ($generalSearch !== '') {
            $roleQuery->where(function ($query) use ($generalSearch) {
                $query->where('r.role_name', 'like', '%' . $generalSearch . '%')
                    ->orWhere('u.user_name', 'like', '%' . $generalSearch . '%');
            });
        }

        if ($sortOrder !== "") {

            if ($sortOrder === 'asc') {
                $roleQuery->orderBy('r.role_name', 'ASC');
            } elseif ($sortOrder === 'desc') {
                $roleQuery->orderBy('r.role_name', 'DESC');
            }
        } else {

            $roleQuery->orderBy('r.role_name', 'ASC');
        }
        if ($iDisplayLength !== null && $iDisplayLength !== -1) {
            $roleQuery->skip($iDisplayStart)->take($iDisplayLength);
        }

        $recordResult = $roleQuery->get();
        $totalCount = DB::table('role as r')
        ->select('r.*', 'u.user_name as added_by_name')
        ->join('users as u', 'u.id', '=', 'r.added_by')->count(); // Get total count without pagination

        $return = [
            "error" => false,
            "data" => $recordResult,
            "totalCount" => $totalCount,
            "query" => $roleQuery->toSql() // Get the SQL query
        ];

        if ($totalCount <= 0) {
            $return['error'] = true;
        }

        return $return;
    }


    public function updated($roleID, $roleName, $status)
    {
        DB::beginTransaction();
        $return = [];

        try {

            $roleResult = DB::table('role')
                ->where('role_name', $roleName)
                ->where('id', '!=', $roleID)->get();

            // var_dump($roleResult->toSql());

            if ($roleResult->isNotEmpty()) {
                $return['message'] = 'Role already present';
                $return['error'] = true;
            } else {
                DB::table('role')
                    ->where('id', '=', $roleID)
                    ->update([
                        'role_name' => $roleName,
                        'status' => $status,
                        'updated_at' => now()
                    ]);
                DB::commit();

                $return['message'] = 'Updated successfully';
                $return['error'] = false;
            }
        } catch (Exception $e) {
            $return['message'] = 'Failed to update ';
            $return['error'] = true;
        } finally {
            DB::rollback();
        }

        return $return;
    }
}
