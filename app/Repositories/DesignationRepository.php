<?php

namespace App\Repositories;


use App\Models\Designation;
use Exception;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDOException;

class DesignationRepository
{
    private Designation $designation;

    public function __construct(Designation $designation)
    {
        $this->designation = $designation;
    }

    public function add($designationName, $departmentID, $userID)
    {
        DB::beginTransaction();
        $return = [];

        try {
            $designationResult = DB::table('designation')
                ->where('designation_name', $designationName)
                ->where('department_id', $departmentID)
                ->get();

            if ($designationResult->isNotEmpty()) {
                $return['message'] = 'Designation already present';
                $return['error'] = true;
            } else {
                // Insert the new designation
                DB::table('designation')->insert([
                    'designation_name' => $designationName,
                    'department_id' => $departmentID,
                    'status' => 'ACTIVE',
                    'added_by' => $userID,
                    'added_on' => now(),
                ]);

                DB::commit();

                $return['message'] = 'Created successfully';
                $return['error'] = false;
            }
        } catch (PDOException $e) {
            $return['message'] = 'Failed to create designation';
            $return['error'] = true;
            DB::rollback();
        }

        return $return;
    }


    public function get($departmentID, $status, $generalSearch, $iDisplayStart, $iDisplayLength, $sortOrder)
    {
        try {
            $roleQuery = DB::table('designation as d')->select('d.*', 'u.user_name as added_by_name', 'dpt.department_name')->join('department as dpt', 'dpt.id', '=', 'd.department_id')->join('users as u', 'u.id', '=', 'd.added_by');

            if ($status !== null && $status !== "") {
                $roleQuery->where('d.status', $status);
            }
            if ($departmentID !== null && $departmentID !== "") {
                $roleQuery->where('d.department_id', $departmentID);
            }

            if ($generalSearch !== '') {
                $roleQuery->where(function ($query) use ($generalSearch) {
                    $query->where('d.designation_name', 'like', '%' . $generalSearch . '%')
                        ->orWhere('u.user_name', 'like', '%' . $generalSearch . '%');
                });
            }

            if ($sortOrder !== "") {

                if ($sortOrder === 'asc') {
                    $roleQuery->orderBy('d.designation_name', 'ASC');
                } elseif ($sortOrder === 'desc') {
                    $roleQuery->orderBy('d.designation_name', 'DESC');
                }
            } else {

                $roleQuery->orderBy('d.designation_name', 'ASC');
            }
            if ($iDisplayLength !== null) {
                $roleQuery->skip($iDisplayStart)->take($iDisplayLength);
            }

            $recordResult = $roleQuery->get();
            $return = [];
            $return["error"] = false;
            if (count($recordResult) <= 0) {
                $return['error'] = true;
            }
            $return["data"] = $recordResult;
            $count = DB::table('designation as d')->join('department as dpt', 'dpt.id', '=', 'd.department_id')->join('users as u', 'u.id', '=', 'd.added_by')->count();
            $return["totalCount"] = $count;
            return $return;
        } catch (PDOException $e) {
            Log::error($e->getMessage());
        }


       
    }

    public function updated($designationID, $designationName, $status)
    {
        DB::beginTransaction();
        $return = [];

        try {

            $roleResult = DB::table('designation')
                ->where('designation_name', $designationName)
                ->where('id', '!=', $designationID)->get();

            if ($roleResult->isNotEmpty()) {
                $return['message'] = 'designation already present';
                $return['error'] = true;
            } else {
                DB::table('role')
                    ->where('id', '=', $designationID)
                    ->update([
                        'role_name' => $designationName,
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
