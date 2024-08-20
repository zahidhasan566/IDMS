<?php

namespace App\Http\Controllers\Roles;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\SubMenuPermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function index(Request $request)
    {
        $take = $request->take;
        return Role::join('RolePermissions','RolePermissions.RoleId','RoleList.RoleId')
            ->select('RoleList.RoleId','RoleList.RoleName')
            ->groupBy('RoleList.RoleId','RoleList.RoleName')
            ->paginate($take);
    }

    public function all()
    {
        return response()->json([
            'data' => RolePermission::all()
        ]);
    }

    public function modalData()
    {
        return response()->json([
            'status' => 'success',
            'allSubMenus' => Menu::whereNotIn('MenuID',['Dashboard'])->with('allSubMenus')->orderBy('MenuOrder','asc')->get()
        ]);
    }

    public function getRoleInfo($roleId)
    {
        return response()->json([
            'data' => RolePermission::where('RoleId',$roleId)->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'roleId' => 'required',
            'selectedSubMenu' => 'required'
        ]);
        try {
            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'RoleId' => $request->roleId,
                    'SubMenuID' => $row,
                    'Active' => 'Y',
                ];
            }
            RolePermission::insert($submenus);
            return response()->json([
                'status' => 'success',
                'message' => 'Role permission has been successfully created.'
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
            'roleId' => 'required',
            'selectedSubMenu' => 'required'
        ]);
        try {
            RolePermission::where('RoleId',$id)->delete();
            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'RoleId' => $request->roleId,
                    'SubMenuID' => $row,
                    'Active' => 'Y',
                ];
            }
            RolePermission::insert($submenus);
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
