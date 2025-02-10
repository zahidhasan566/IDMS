<?php

use \App\Http\Controllers\Roles\RolePermissionController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api'],'prefix' => 'role-permission'], function () {
    Route::post('list', [RolePermissionController::class, 'index']);
    Route::get('get-role-info/{roleId}', [RolePermissionController::class, 'getRoleInfo']);
    Route::post('create', [RolePermissionController::class, 'store']);
    Route::post('update/{roleId}', [RolePermissionController::class, 'update']);
    Route::get('modal-data',[RolePermissionController::class,'modalData']);
    Route::get('get-role-info',[RolePermissionController::class,'getRoleInfo']);
});
