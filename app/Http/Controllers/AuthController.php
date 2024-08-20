<?php

namespace App\Http\Controllers;

use App\Models\CurrentPeriod;
use App\Models\User;
use App\Models\UserLog;
use App\Services\AccessLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function changePass(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'oldPass' => 'required',
            'newPass' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        $authUser = JWTAuth::parseToken()->authenticate();

        $check = Hash::check($request->oldPass, $authUser->Password);
        if ($check) {
            $user = User::find($authUser->UserID);
            $user->Password = bcrypt($request->newPass);
            $user->save();
            return response()->json(['message' => "Password changed successfully"]);
        } else {
            return response()->json(['message' => "Your current password is Invalid"], 400);
        }
    }

    public function changeProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'image' => 'required',
            'isImageChange' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        $authUser = JWTAuth::parseToken()->authenticate();
        $user = User::find($authUser->UserID);
        $user->UserName = $request->name;
        $user->Email = $request->email;
        $user->Phone = $request->mobile;
        if ($request->isImageChange) {
            if ($authUser->Avatar !== 'default.png') {
                try {
                    unlink(public_path('uploads/') . $authUser->Avatar);
                } catch (\Exception $e) {
                }
            }
            $image = $request->image;
            $name = rand(0, 10000000) . time() . '.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
            Image::make($image)->save(public_path('uploads/') . $name);
            $user->Avatar = $name;
        }
        $user->save();
        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string|min:4',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        $userId = $request->username;
        $password = $request->password;
        $user = DB::select("SELECT dbo.ufn_PasswordDecode(Password) as DecodPassword,* FROM UserManager where UserId='$userId' AND ACTIVE = '0'");
        if ($user) {
            if ($user[0]->DecodPassword == $password) {
                $user = User::where('UserId', $userId)->first();
                Auth::login($user);
                $token = JWTAuth::fromUser($user);
                return $this->respondWithToken($token);
            } else {
                return response()->json([
                    'status' => 'error',
                    'data' => [],
                    'message' => 'Invalid User ID or Password!'
                ],401);
            }
        }
        return response()->json([
            'status' => 'error',
            'data' => [],
            'message' => 'No user found!'
        ],500);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'designation' => 'required',
            'email' => 'required',
            'password' => 'required|string|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }

        try {
            $user = new User();
            $user->Name = $request->name;
            $user->Designation = $request->designation;
            $user->Email = $request->email;
            $user->Password = bcrypt($request->password);
            $user->Status = 'Y';
            $user->CreatedBy = 1;
            $user->UpdatedBy = 1;
            $user->UserType = 'default';
            $user->Avatar = 'default.png';
            $user->save();
            return response()->json(['message' => "success"]);

        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function me()
    {
        return response()->json($this->guard()->user());

    }

    public function logout()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            UserLog::create(['UserId' => $user->ID, 'TransactionTime' => Carbon::now(), 'TransactionDetails' => "Logged Out"]);
            $this->guard()->logout();
        } catch (\Exception $exception) {

        }
        return response()->json(['message' => 'Successfully logged out']);
    }


    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $this->guard()->factory()->getTTL() * 60
        ]);
    }

    public function guard()
    {
        return Auth::guard('api');
    }
}
