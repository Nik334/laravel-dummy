<?php

namespace App\Repositories;

use App\Models\Department;
use Exception;

use Illuminate\Support\Facades\DB;
use PDOException;

class DepartmentRepository
{
    private Department $department;

    public function __construct(Department $department)
    {
        $this->department = $department;
    }

    public function add($departmentName, $userID)
    {
        DB::beginTransaction();
        $return = [];

        try {
            $departmentResult = DB::table('department')
                ->where('department_name', $departmentName)
                ->get();

            if ($departmentResult->isNotEmpty()) {
                $return['message'] = 'Department already present';
                $return['error'] = true;
            } else {
                // Insert the new department
                DB::table('department')->insert([
                    'department_name' => $departmentName,
                    'status' => 'ACTIVE',
                    'added_by' => $userID,
                    'added_on' => now(),
                ]);

                DB::commit();

                $return['message'] = 'Department created successfully';
                $return['error'] = false;
            }
        } catch (PDOException $e) {
            $return['message'] = 'Failed to create department';
            $return['error'] = true;
            DB::rollback();
        }

        return $return;
    }


    public function get($status, $generalSearch, $iDisplayStart, $iDisplayLength, $sortOrder)
    {
        $roleQuery = DB::table('department as d')->select('d.*', 'u.user_name as added_by_name')->join('users as u', 'u.id', '=', 'd.added_by');

        if ($status !== null && $status !== "") {
            $roleQuery->where('d.status', $status);
        }

        if ($generalSearch !== '') {
            $roleQuery->where(function ($query) use ($generalSearch) {
                $query->where('d.department_name', 'like', '%' . $generalSearch . '%')
                    ->orWhere('u.user_name', 'like', '%' . $generalSearch . '%');
            });
        }

        if ($sortOrder !== "") {

            if ($sortOrder === 'asc') {
                $roleQuery->orderBy('d.department_name', 'ASC');
            } elseif ($sortOrder === 'desc') {
                $roleQuery->orderBy('d.department_name', 'DESC');
            }
        } else {

            $roleQuery->orderBy('d.department_name', 'ASC');
        }
        if ($iDisplayLength !== null && $iDisplayLength !== -1) {
            $roleQuery->skip($iDisplayStart)->take($iDisplayLength);
        }
      
        $recordResult = $roleQuery->get();
        // $recordResult = $roleQuery->offset($iDisplayStart)->limit($iDisplayEnd)-get();
        $return = [];
        $return["error"] = false;
        if (count($recordResult) <= 0) {
            $return['error'] = true;
        }
        $return["data"] = $recordResult;
        $count = DB::table('department as d')->join('users as u', 'u.id', '=', 'd.added_by')->count();
        $return["totalCount"] = $count;

        return $return;
    }

    public function updated($departmentID, $departmentName, $status)
    {
        DB::beginTransaction();
        $return = [];

        try {

            $roleResult = DB::table('department')
                ->where('department_name', $departmentName)
                ->where('id', '!=', $departmentID)->get();

            if ($roleResult->isNotEmpty()) {
                $return['message'] = 'Department already present';
                $return['error'] = true;
            } else {
                DB::table('role')
                    ->where('id', '=', $departmentID)
                    ->update([
                        'role_name' => $departmentName,
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
