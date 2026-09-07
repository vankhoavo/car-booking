<?php

use App\Http\Controllers\AdminBlogController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminRentalController;
use App\Http\Controllers\AdminVehicleController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RentalController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\Teams\TeamInvitationController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureTeamMembership;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'vehicles' => Vehicle::query()
            ->where('status', 'available')
            ->orderBy('name')
            ->limit(6)
            ->get(['id', 'name', 'brand', 'model', 'type', 'seats', 'image', 'price']),
    ]);
})->name('home');
Route::get('/dat-xe', [BookingController::class, 'create'])->name('booking.index');
Route::post('/dat-xe', [BookingController::class, 'store'])->name('booking.store');
Route::get('/thue-xe', [RentalController::class, 'create'])->name('rental.index');
Route::post('/thue-xe', [RentalController::class, 'store'])->name('rental.store');
Route::get('/tour', fn () => redirect('/'))->name('tour.index');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

Route::prefix('api')->group(function () {
    Route::get('/vehicles', [VehicleController::class, 'index']);
    Route::get('/vehicles/{vehicle}', [VehicleController::class, 'show']);
    Route::get('/vehicles/{vehicle}/availability', [VehicleController::class, 'availability']);
});

Route::prefix('admin')
    ->middleware(['auth', 'verified', EnsureAdmin::class])
    ->group(function () {
        Route::get('/', AdminController::class)->name('admin.dashboard');
        Route::get('/vehicles', [AdminVehicleController::class, 'index'])->name('admin.vehicles.index');
        Route::post('/vehicles', [AdminVehicleController::class, 'store'])->name('admin.vehicles.store');
        Route::put('/vehicles/{vehicle}', [AdminVehicleController::class, 'update'])->name('admin.vehicles.update');
        Route::delete('/vehicles/{vehicle}', [AdminVehicleController::class, 'destroy'])->name('admin.vehicles.destroy');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings.index');
        Route::put('/bookings/{booking}', [AdminBookingController::class, 'update'])->name('admin.bookings.update');
        Route::get('/rentals', [AdminRentalController::class, 'index'])->name('admin.rentals.index');
        Route::put('/rentals/{rental}', [AdminRentalController::class, 'update'])->name('admin.rentals.update');
        Route::get('/blog', [AdminBlogController::class, 'index'])->name('admin.blog.index');
        Route::post('/blog', [AdminBlogController::class, 'store'])->name('admin.blog.store');
        Route::put('/blog/{blogPost}', [AdminBlogController::class, 'update'])->name('admin.blog.update');
        Route::delete('/blog/{blogPost}', [AdminBlogController::class, 'destroy'])->name('admin.blog.destroy');
    });

Route::prefix('{current_team}')
    ->middleware(['auth', 'verified', EnsureTeamMembership::class])
    ->group(function () {
        Route::get('dashboard', DashboardController::class)->name('dashboard');
    });

Route::middleware(['auth'])->group(function () {
    Route::post('invitations/{invitation}/accept', [TeamInvitationController::class, 'accept'])->name('invitations.accept');
    Route::delete('invitations/{invitation}', [TeamInvitationController::class, 'decline'])->name('invitations.decline');
});

require __DIR__.'/settings.php';
