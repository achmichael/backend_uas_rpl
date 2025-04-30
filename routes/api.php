<?php
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\CatalogController;
use App\Http\Controllers\API\CertificateController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\ContractController;
use App\Http\Controllers\API\FreelancerController;
use App\Http\Controllers\API\JobController;
use App\Http\Controllers\API\LocationController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PortofolioController;
use App\Http\Controllers\API\PostController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\UserProfileController;
use App\Http\Controllers\API\UserSkillController;
use App\Http\Controllers\API\ApplicationController;
use App\Http\Controllers\API\AIController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('ai')->group(function() {
    Route::post('/chat', [AIController::class, 'chat']);
});

Route::prefix('jobs')->group(function () {
    Route::get('/', [JobController::class, 'index']);
    Route::get('/{id}', [JobController::class, 'show']);
});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{id}', [PostController::class, 'show']);
});

Route::get('/verify-token', [AuthController::class, 'verifyToken']);

Route::middleware(['jwt.auth'])->group(function () {
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
        Route::post('/', [JobController::class, 'create']);
        Route::put('/{id}', [JobController::class, 'update']);
        Route::delete('/{id}', [JobController::class, 'delete']);
        Route::post('/company-jobs', [JobController::class, 'jobsByCompany']);
    });

    Route::prefix('companies')->group(function () {
        Route::get('/', [CompanyController::class, 'index']);
        Route::post('/', [CompanyController::class, 'create']);
        Route::get('/{id}', [CompanyController::class, 'show']);
        Route::put('/{id}', [CompanyController::class, 'update']);
        Route::delete('/{id}', [CompanyController::class, 'delete']);
    });

    Route::prefix('posts')->group(function () {
        Route::post('/', [PostController::class, 'store']);
        Route::put('/{id}', [PostController::class, 'update']);
        Route::delete('/{id}', [PostController::class, 'destroy']);
        Route::get('/freelancer/{id}', [PostController::class, 'recommendFreelancer']);
    });

    Route::prefix('freelancers')->group(function () {
        Route::get('/', [FreelancerController::class, 'index']);
        Route::get('/{id}', [FreelancerController::class, 'show']);
        Route::post('/', [FreelancerController::class, 'store']);
        Route::put('/{id}', [FreelancerController::class, 'update']);
        Route::delete('/{id}', [FreelancerController::class, 'destroy']);
    });

    Route::prefix('contracts')->group(function () {
        Route::get('/', [ContractController::class, 'contractByUser']);
        Route::post('/', [ContractController::class, 'add']);
        Route::get('/{id}', [ContractController::class, 'show']);
    });

    Route::prefix('payments')->group(function (){
        Route::post('/pay', [PaymentController::class, 'getSnapToken']);
        // this route can be used to check the payment status after the payment is made, and can be configured in the payment provider dashboard
        Route::post('/callback', [PaymentController::class, 'handleCallback']);
    });

    Route::prefix('users')->group(function () {
        Route::prefix('skills')->group(function () {
            Route::get('/', [UserSkillController::class, 'index']);
            Route::post('/', [UserSkillController::class, 'store']);
            Route::put('/{id}', [UserSkillController::class, 'update']);
            Route::delete('/{id}', [UserSkillController::class, 'destroy']);
        });
        Route::put('/{id}', [UserController::class, 'update']);
        Route::get('/{id}', [UserController::class, 'show']);
<<<<<<< HEAD
=======
    });

    Route::prefix('applications')->group(function () {
        Route::post('/', [ApplicationController::class, 'create']);
        Route::put('/{id}', [ApplicationController::class, 'update']);
        Route::post('/{id}/change-state', [ApplicationController::class, 'changeState']);
        Route::delete('/{id}', [ApplicationController::class, 'delete']);
        Route::get('/{id}', [ApplicationController::class, 'show']);
>>>>>>> 42801cf6aa7ddd39678eca989aeb5e217fcf4bc5
    });
});
// Route fallback execute when route not found in routes in api.php
Route::fallback(function () {
    return response()->json([
        'status'  => 'error',
        'message' => 'Route not found',
    ], 404);
});
