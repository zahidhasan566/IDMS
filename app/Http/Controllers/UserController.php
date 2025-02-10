<?php

namespace App\Http\Controllers;

use App\Models\Advances;
use App\Models\Banks;
use App\Models\Branch;
use App\Models\DealerUser;
use App\Models\Menu;
use App\Models\SubMenu;
use App\Models\SubMenuPermission;
use App\Models\User;
use App\Models\UserBusiness;
use App\Models\UserDepartment;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        try {
            $check = User::where('UserID', $request->value)->exists();
            return response()->json(['status' => $check], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Oops! Something went wrong'], 400);
        }
    }

    public function allUsers()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->UserType == 'admin') {
            return User::select('ID', 'Name', 'Designation')->get();
        } else {
            return User::where('UserType', '!=', 'admin')
                ->select('ID', 'Name', 'Designation')
                ->get();
        }
    }
    public function userPasswordCheck()
    {
        $userid = Auth::user()->UserId;
        $sql = "select  LastPasswordUpdated
                From UserManager where userid = '$userid'
                                   AND DATEDIFF(DAY,LastPasswordUpdated,GETDATE()) >= 15
              
                                 ";
        $date =DB::select($sql);

        return response()->json([
            'data' => $date
        ]);

    }
    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $query = User::leftJoin('RoleList', 'RoleList.RoleId', 'UserManager.RoleId')
            ->where(function ($q) use ($search) {
                $q->where('UserId', 'like', '%' . $search . '%');
                $q->orWhere('UserName', 'like', '%' . $search . '%');
                $q->orWhere('Designation', 'like', '%' . $search . '%');
                $q->orWhere('RoleList.RoleName', 'like', '%' . $search . '%');
            })
            ->where('userId','!=','su')
            ->select('UserId', 'UserName', 'Designation', 'RoleList.RoleName');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $query->get()
            ]);
        }
        return $query->paginate($take);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
            'userName' => 'required|string',
            'designation' => 'required',
            'role' => 'required',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            if (User::where('UserId', $request->userId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This user is already exists!'
                ], 400);
            }
            DB::beginTransaction();
//            $sql = "INSERT INTO UserManager ([UserId], [UserName], [Designation], [Password], [RoleId]) VALUES ('$request->userId','$request->userName','$request->designation',dbo.ufn)";
            $user = new User();
            $user->UserId = $request->userId;
            $user->UserName = $request->userName;
            $user->Designation = $request->designation;
            $password = $request->password;
            $password = DB::select("SELECT dbo.ufn_PasswordEncode(?) as EncodedPassword", [$password])[0]->EncodedPassword;
            $password = str_replace('?','',$password);
            $user->Password = $password;
            $user->RoleId = $request->role;
            $user->Active = 0;
            $user->save();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
            'userName' => 'required|string',
            'designation' => 'required',
            'role' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $user = User::find($request->userId);
            $user->UserName = $request->userName;
            $user->Designation = $request->designation;
            $user->RoleId = $request->role;
            $user->save();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'staffId' => 'required|string',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }
            $user = User::find($request->staffId)->UserId;
            $sql = "update UserManager set Password = dbo.ufn_PasswordEncode('".$request->password."')
                    where UserId = '".$user."'";
            DB::statement($sql);
            return response()->json([
                'status' => 'success',
                'message' => 'Password Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function passwordChange(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'userId' => 'required|string',
            'updatePassword' => 'required|string',
            'confirmUpdatePassword' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }

        if($request->updatePassword ===$request->confirmUpdatePassword){
            try{
                $updatePassword = $request->updatePassword;
                $userId = Auth::user()->UserId;
                $sql = "update UserManager set Password = dbo.ufn_PasswordEncode('".$updatePassword."'),
                 LastPasswordUpdated = GetDate()
                 where UserId = '".$userId."'";
                DB::statement($sql);
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password Updated Successfully'
                ]);
            }
            catch (\Exception $exception){
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
        else{
            return response()->json(['message' => 'Password did not match'], 400);
        }

    }

    public function delete($id)
    {
        if (false) {
            return response()->json(['message' => "User is already used!"], 500);
        } else {
            User::where('id', $id)->delete();
            return response()->json(['message' => "User deleted successfully"]);
        }
    }

    public function getUserInfo($staffId)
    {
        $user = User::where('UserId', $staffId)->first();
        $allSubMenu = [];
        if ($user->RoleId === 'dealeruser') {
            $allSubMenu = DealerUser::where('DealerId',Auth::user()->UserId)->where('UserId',$user->UserId)->get();
        }
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'allSubMenu' => $allSubMenu
        ]);
    }
}
