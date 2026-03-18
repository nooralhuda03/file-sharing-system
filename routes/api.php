<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\FolderController;
use App\Http\Controllers\Api\UserController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::post('import-users', [UserController::class, 'importUsers']);

Route::get('files/{id}/download', [FileController::class, 'download']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('user', [AuthController::class, 'user']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('folders', FolderController::class);

    Route::get('folders/{folder}/files', [FolderController::class, 'files']);
    Route::post('folders/{folder}/files', [FileController::class, 'store']);

    Route::apiResource('folders/{folder}/files', FileController::class);
    Route::get('folders/{folder}/files/{id}', [FileController::class, 'show']);
    Route::put('folders/{folder}/files/{id}', [FileController::class, 'update']);
    Route::delete('folders/{folder}/files/{id}', [FileController::class, 'destroy']);
    Route::get('folders/{folder}/files/{id}/download', [FileController::class, 'download']);
    Route::patch('folders/{folder}/files/{id}/visibility', [FileController::class, 'setVisibility']);

});
