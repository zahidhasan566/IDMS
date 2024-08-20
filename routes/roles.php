<?php

use \App\Http\Controllers\Roles\RoleController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['jwt:api'],'prefix' => 'roles'], function () {
    Route::post('list', [RoleController::class, 'index']);
    Route::get('get-role-info/{roleId}', [RoleController::class, 'getRoleInfo']);
    Route::post('create', [RoleController::class, 'store']);
    Route::post('update/{roleId}', [RoleController::class, 'update']);
    Route::get('all', [RoleController::class, 'all']);
    Route::post('permission', [RoleController::class, 'permissions']);
});
