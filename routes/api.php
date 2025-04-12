<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CatalogController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\PortofolioController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\UserProfileController;

Route::get('/', function (Request $request) {
    return $request->user();
});

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

Route::post('/register', [AuthController::class, 'register'])->name('register')->middleware('web');
Route::post('/login', [AuthController::class, 'login'])->middleware('web');

Route::prefix('portofolios')->group(function () {
    Route::post('/', [PortofolioController::class, 'create']);
    Route::put('/{id}', [PortofolioController::class, 'update']);
    Route::delete('/{id}', [PortofolioController::class, 'delete']);
});

Route::prefix('user_profiles')->group(function () {
    Route::post('/', [UserProfileController::class, 'create']);
});

Route::prefix('certificates')->group(function () {
    Route::post('/', [CertificateController::class, 'certificate']);
    Route::put('/{id}', [CertificateController::class, 'update']);
    Route::delete('/{id}', [CertificateController::class, 'delete']);
});

Route::prefix('catalogs')->group(function () {
    Route::post('/', [CatalogController::class, 'catalog']);
    Route::put('/{id}', [CatalogController::class, 'update']);
    Route::delete('/{id}', [CatalogController::class, 'delete']);
});

Route::prefix('locations')->group(function () {
    Route::post('/', [LocationController::class, 'create']);
    Route::put('/{id}', [LocationController::class, 'update']);
    Route::delete('/{id}', [LocationController::class, 'delete']);

});

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/{id}', [JobController::class, 'show']);
    Route::post('/', [JobController::class, 'create']);
    Route::put('/{id}', [JobController::class, 'update']);
    Route::delete('/{id}', [JobController::class, 'delete']);
});

Route::prefix('companies')->group(function () {
    Route::get('/', [CompanyController::class, 'index']);
    Route::post('/', [CompanyController::class, 'create']);
    Route::get('/{id}', [CompanyController::class, 'show']);
    Route::put('/{id}', [CompanyController::class, 'update']);
    Route::delete('/{id}', [CompanyController::class, 'delete']);
});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/show-post/{id}', [PostController::class, 'show']);
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

Route::prefix('users')->group(function () {
    Route::put('/{id}', [UserController::class, 'update']);
    Route::get('/{id}', [UserController::class, 'show']);
});

// Route fallback execute when route not found in routes in api.php
Route::fallback(function () {
    return response()->json([
        'status' => 'error',
        'message' => 'Route not found',
    ], 404);
});
