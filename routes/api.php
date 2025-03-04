<?php

use App\Http\Controllers\API\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\PostController;

Route::get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
// with resource to create 7 routes for PostController
/* HTTP Method	URI	Controller Method	Deskripsi
GET	/posts	index	Menampilkan semua data
GET	/posts/create	create	Menampilkan form tambah data
POST	/posts	store	Proses simpan data baru
GET	/posts/{id}	show	Menampilkan data detail
GET	/posts/{id}/edit	edit	Menampilkan form edit data
PUT/PATCH	/posts/{id}	update	Proses update data
DELETE	/posts/{id}	destroy	Proses hapus data
*/
Route::resource('posts', PostController::class); 

// Route fallback execute when route not found in routes in api.php
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Route not found',
    ], 404);
});