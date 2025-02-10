<?php

namespace App\Http\Controllers;

use App\Models\DealerUser;
use App\Models\User;
use App\Traits\CodeGeneration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DealerUsersController extends Controller
{
    use CodeGeneration;

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
            ->whereRaw("LEFT(UserManager.UserId,6) = '" . Auth::user()->UserId . "'")
            ->where('UserManager.RoleId', 'dealeruser')
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
        $request->validate([
            'userName' => 'required',
            'designation' => 'required',
            'menus' => 'required',
            'password' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $auth = Auth::user();
            $passwordData = DB::select("select dbo.ufn_PasswordEncode('$request->password') as RawPass");
            //CREATE USER
            $userId = $this->generateDealerUserCode($auth->UserId);
            $user = new User();
            $user->UserId = $userId;
            $user->UserName = $request->userName;
            $user->Designation = $request->designation;
            $user->Password = $passwordData[0]->RawPass;
            $user->RoleId = 'dealeruser';
            $user->Active = '0';
            $user->save();
            //DEALER USER PERMISSION
            if (count($request->menus)) {
                foreach ($request->menus as $menu) {
                    DealerUser::create([
                        'DealerId' => $auth->UserId,
                        'UserId' => $userId,
                        'SubMenuID' => $menu,
                        'Active' => 'Y'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No Menus Selected!'
                ],400);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Dealer user has been successfully created.'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function update(Request $request,$userId)
    {
        $request->validate([
            'userName' => 'required',
            'designation' => 'required',
            'menus' => 'required'
        ]);
        DB::beginTransaction();
        try {
            $auth = Auth::user();
            //CREATE USER
            $user = User::find($userId);
            $user->UserName = $request->userName;
            $user->Designation = $request->designation;
            $user->save();
            //DEALER USER PERMISSION
            DealerUser::where('DealerId',$auth->UserId)->where('UserId',$userId)->delete();
            if (count($request->menus)) {
                foreach ($request->menus as $menu) {
                    DealerUser::create([
                        'DealerId' => $auth->UserId,
                        'UserId' => $userId,
                        'SubMenuID' => $menu,
                        'Active' => 'Y'
                    ]);
                }
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No Menus Selected!'
                ],400);
            }
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Dealer user has been successfully updated.'
            ]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
