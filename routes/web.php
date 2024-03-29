<?php

use App\Models\Home;
use App\Models\Clothes;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DashboardOrderController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ClothesController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\DashboardHomeController;
use App\Http\Controllers\DashboardUserController;
use App\Http\Controllers\DashboardClothesController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\DashboardAdminProfileController;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {

    return view('home', [
        'title' => 'Home',
        "active" => "home",
        "clothes" => Clothes::latest()->take(4)->get(),
        'categories' => Category::take(3)->get(),
        'home' =>  Home::first(),
        'count' => Home::all()->count()
    ]);
});

//CLothes Controller 
Route::get('clothes/', [ClothesController::class, 'index']);
Route::get('clothes/{clothes:slug}', [ClothesController::class, 'show']);

Route::get('/categories', function () {
    return view('categories', [
        'title' => 'Categories',
        "active" => "categories",
        'categories' => Category::all()
    ]);
});

// PROFILE
Route::resource('/profile', UserController::class)->middleware('auth');
Route::get('/purchase', [UserController::class, 'purchase'])->middleware('auth', 'verified');

Route::get('/purchase/{user}', [UserController::class, 'showPurchase'])->middleware('auth', 'verified');
// halaman address
Route::get('/address', [UserController::class, 'address'])->middleware('auth', 'verified');
Route::get('/address/{user}', [UserController::class, 'editAddress'])->middleware('auth', 'verified');
Route::post('/address/{user}', [UserController::class, 'updateAddress'])->name('address.update')->middleware('auth', 'verified');




// login
Route::get('/login', [LoginController::class, 'index'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate']);
Route::post('/logout', [LoginController::class, 'logout']);

// register
Route::get('/register', [RegisterController::class, 'index'])->middleware('guest');
Route::post('/register', [RegisterController::class, 'store']);
// register to get API city
Route::get('/city', [RegisterController::class, 'city']);

// Email verify
Route::get('/email/verify', function () {
    return view('auth-verify-email', [
        'title' => 'Unverify',
        "active" => "Unverify",
    ]);
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/profile');
})->middleware(['auth', 'signed'])->name('verification.verify');

// resend email verify
Route::get('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return view('resend-email-verification', [
        'title' => 'Resend Email',
        "active" => "Resend Email",
    ]);
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// manage Cart
Route::resource('/booking', BookingController::class)->middleware('auth');
Route::get('/checkout', [BookingController::class, 'checkout'])->middleware('auth', 'verified');
Route::get('/shipping', [BookingController::class, 'shipping'])->middleware('auth', 'verified');
// payment midtrans
Route::get('/payment', [PaymentController::class, 'payment'])->middleware('auth', 'verified');
// Route::get('/dashboard/order', [OrderController::class, 'index'])->middleware('admin');
Route::post('/payment', [PaymentController::class, 'payment_post'])->middleware('auth', 'verified');





// ADMINSTRATOR //

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->middleware('admin');


Route::get('/dashboard/clothes/checkSlug', [DashboardClothesController::class, 'checkSlug'])->middleware('admin');
Route::get('/dashboard/clothes/export/', [DashboardClothesController::class, 'export_excel'])->middleware('admin');
// route clothes
Route::resource('/dashboard/clothes', DashboardClothesController::class)->middleware('admin');

Route::resource('/dashboard/categories', AdminCategoryController::class)->middleware('admin')->except('show');

Route::get('/dashboard/order/export/', [DashboardOrderController::class, 'export_excel'])->middleware('admin');
Route::resource('/dashboard/order', DashboardOrderController::class)->middleware('admin');

Route::resource('/dashboard/home', DashboardHomeController::class)->middleware('admin');

Route::get('/dashboard/users/export/', [DashboardUserController::class, 'export_excel'])->middleware('admin');
Route::resource('/dashboard/users', DashboardUserController::class)->middleware('admin');

Route::resource('/dashboard/admins', DashboardAdminProfileController::class)->middleware('admin');
