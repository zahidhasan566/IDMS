<?php

namespace App\Services;

use App\Models\Advances;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class DepartmentService
{
    public static function list() {
        $department = Department::all();
        return $department;
    }

    public static function departments() {
        return Advances::select(DB::raw("distinct ResStaffDepartment"))->get();
    }
}