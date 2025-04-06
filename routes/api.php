<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\FreelancerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('web');
Route::post('/login', [AuthController::class, 'login'])->middleware('web');

// with resource to create 7 routes for PostController
// Route::resource('posts', PostController::class);
/* HTTP Method	URI	Controller Method	Deskripsi
GET	/posts	index	Menampilkan semua data
GET	/posts/create	create	Menampilkan form tambah data
POST	/posts	store	Proses simpan data baru
GET	/posts/{id}	show	Menampilkan data detail
GET	/posts/{id}/edit	edit	Menampilkan form edit data
PUT/PATCH	/posts/{id}	update	Proses update data
DELETE	/posts/{id}	destroy	Proses hapus data
*/

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
    Route::post('/', [PostController::class, 'store']);
    Route::put('/{id}', [PostController::class, 'update']);
    Route::delete('/{id}', [PostController::class, 'destroy']);
    Route::get('/freelancer/{id}', [PostController::class, 'recommendFreelancer']);
});

Route::prefix('freelancers')->group(function (){
    Route::get('/', [FreelancerController::class, 'index']);
    Route::get('/{id}', [FreelancerController::class, 'show']);
    Route::post('/', [FreelancerController::class, 'store']);
    Route::put('/{id}', [FreelancerController::class, 'update']);
    Route::delete('/{id}', [FreelancerController::class, 'destroy']);
});

Route::prefix('contracts')->group(function () {
    Route::post('/', [ContractController::class, 'add']);
    Route::get('/{id}', [ContractController::class, 'show']);
});

// Route fallback execute when route not found in routes in api.php
Route::fallback(function () {
    return response()->json([
        'status'  => 'error',
        'message' => 'Route not found',
    ], 404);
});
