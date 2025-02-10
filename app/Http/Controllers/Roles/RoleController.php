<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        return Role::paginate($take);
    }

    public function all()
    {
        return response()->json([
            'data' => Role::all()
        ]);
    }

    public function getRoleInfo($roleId)
    {
        return response()->json([
            'data' => Role::where('RoleId',$roleId)->first()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'roleId' => 'required',
            'roleName' => 'required'
        ]);
        try {
            Role::create([
                'RoleId' => $request->roleId,
                'RoleName' => $request->roleName,
                'RoleDescription' => $request->roleDescription,
                'CreatedAt' => Carbon::now(),
                'UpdatedAt' => Carbon::now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Role has been successfully created.'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'roleName' => 'required'
        ]);
        try {
            Role::where('RoleId',$id)->update([
                'RoleId' => $request->roleId,
                'RoleName' => $request->roleName,
                'RoleDescription' => $request->roleDescription,
                'UpdatedAt' => Carbon::now()
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Role has been successfully updated.'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ],500);
        }
    }
}
