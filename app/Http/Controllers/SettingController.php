<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuPermission;
use App\Models\OutletIP;
use App\Models\Particular;
use App\Models\User;
use App\Models\UserMenu;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Tymon\JWTAuth\Facades\JWTAuth;

class SettingController extends Controller
{
    public function menuPermission(Request $request)
    {
        $path = $request->name;
        $user = JWTAuth::parseToken()->authenticate();
        $checkPermission = UserMenu::join('MenuItem', 'MenuItem.ID', 'UserMenu.MenuItemId')
            ->where('UserMenu.UserId', $user->ID)
            ->where('MenuItem.Link', $path)
            ->exists();
        if ($checkPermission) {
            return response()->json(['message' => "menu found"], 200);
        } else {
            return response()->json(['message' => "menu not found"], 400);
        }
    }

    public function appSupportingData()
    {
        try {
            $auth = Auth::user();
            $query = Menu::select('Menus.*');
            $data = $query->with('subMenus')
                ->orderBy('MenuOrder','asc')
                ->get();
            return response()->json([
                'status' => 'success',
                'menus' => $data,
                'user' => User::where('UserId',$auth->UserId)->first()
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function imageUpload($image, $namePrefix, $destination)
    {

        list($type, $file) = explode(';', $image);
        list(, $extension) = explode('/', $type);
        list(, $file) = explode(',', $file);
        $fileNameToStore = $namePrefix . strtotime(Carbon::now()) . rand(0, 100000000) . '.' . $extension;
        $source = fopen($image, 'r');
        $destination = fopen($destination . $fileNameToStore, 'w');
        stream_copy_to_stream($source, $destination);
        fclose($source);
        fclose($destination);
        return $fileNameToStore;
    }
}
