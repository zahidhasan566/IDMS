<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory;
    protected $table = "Menus";
    public $primaryKey = 'MenuID';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';

    public function subMenus() {
        if (Auth::user()->RoleId === 'dealeruser') {
            $userId = Auth::user()->UserId;
            return $this->hasMany(SubMenu::class,'MenuID','MenuID')
                ->join('DealerPermission',function ($q) use ($userId) {
                    $q->on('DealerPermission.SubMenuID','SubMenus.SubMenuID')->where('DealerPermission.UserId',$userId);
                })
                ->where('Status',1)
                ->orderBy('SubMenuOrder');
        } else {
            $userId = Auth::user()->RoleId;
            return $this->hasMany(SubMenu::class,'MenuID','MenuID')
                ->join('RolePermissions',function ($q) use ($userId) {
                    $q->on('RolePermissions.SubMenuID','SubMenus.SubMenuID')->where('RolePermissions.RoleId',$userId);
                })
                ->where('Status',1)
                ->orderBy('SubMenuOrder');
        }
    }

    public function allSubMenus() {
        return $this->hasMany(SubMenu::class,'MenuID','MenuID')->where('Status',1);
    }
}
