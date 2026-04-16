<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CatamaranController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CatamaranController as AdminCatamaranController;
use App\Http\Controllers\Admin\AvailabilityController;
use App\Http\Controllers\Admin\AddonController;
use App\Http\Controllers\Admin\DiscountController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\SettingsController;
use App\Livewire\Public\BookingWizard;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Catamarans
Route::get('/catamarani', [CatamaranController::class, 'index'])->name('catamarans.index');
Route::get('/catamarani/{slug}', [CatamaranController::class, 'show'])->name('catamarans.show');

// Booking Flow (Livewire)
Route::get('/prenota', BookingWizard::class)->name('booking.start');
Route::get('/prenota/{catamaran:slug}', BookingWizard::class)->name('booking.catamaran');

// Booking Confirmation & Management
Route::get('/prenotazione/{booking:uuid}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/prenotazione/{booking:uuid}/conferma', [BookingController::class, 'confirmation'])->name('booking.confirmation');
Route::get('/prenotazione/{booking:uuid}/qr', [BookingController::class, 'qrCode'])->name('booking.qr');

// Payment Routes
Route::prefix('pagamento')->name('payment.')->group(function () {
    Route::get('/{booking:uuid}', [PaymentController::class, 'show'])->name('show');
    Route::post('/{booking:uuid}/process', [PaymentController::class, 'process'])->name('process');
    Route::get('/{booking:uuid}/success', [PaymentController::class, 'success'])->name('success');
    Route::get('/{booking:uuid}/cancel', [PaymentController::class, 'cancel'])->name('cancel');
});

// Stripe Webhook
Route::post('/webhooks/stripe', [PaymentController::class, 'webhook'])->name('webhooks.stripe');

// Check-in (public QR landing)
Route::get('/checkin/{qrCode}', [CheckInController::class, 'verify'])->name('checkin.verify');

// Static Pages
Route::get('/esperienze', [PageController::class, 'experiences'])->name('experiences');
Route::get('/chi-siamo', [PageController::class, 'about'])->name('about');
Route::get('/contatti', [PageController::class, 'contact'])->name('contact');
Route::post('/contatti', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/termini-condizioni', [PageController::class, 'terms'])->name('terms');
Route::get('/cookie-policy', [PageController::class, 'cookies'])->name('cookies');

// Availability API (for calendar)
Route::get('/api/availability/{catamaran}', [BookingController::class, 'availability'])->name('api.availability');
Route::get('/api/availability/{catamaran}/{date}', [BookingController::class, 'dayAvailability'])->name('api.availability.day');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    // My Bookings
    Route::get('/le-mie-prenotazioni', [BookingController::class, 'myBookings'])->name('bookings.my');
    Route::post('/prenotazione/{booking:uuid}/cancel', [BookingController::class, 'cancel'])->name('booking.cancel');
    
    // Profile
    Route::get('/profilo', [PageController::class, 'profile'])->name('profile');
    Route::put('/profilo', [PageController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'verified', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    
    // Today's Schedule
    Route::get('/programma', [DashboardController::class, 'schedule'])->name('schedule');
    
    // Check-in (QR Scanner)
    Route::get('/checkin', [CheckInController::class, 'scanner'])->name('checkin');
    Route::post('/checkin/verify', [CheckInController::class, 'verifyAndCheckIn'])->name('checkin.verify');
    Route::post('/checkin/manual', [CheckInController::class, 'manualCheckIn'])->name('checkin.manual');
    
    // Bookings Management
    Route::resource('bookings', AdminBookingController::class)->except(['create', 'store']);
    Route::post('/bookings/{booking}/confirm', [AdminBookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/refund', [AdminBookingController::class, 'refund'])->name('bookings.refund');
    Route::post('/bookings/{booking}/resend-confirmation', [AdminBookingController::class, 'resendConfirmation'])->name('bookings.resend');
    Route::get('/bookings/{booking}/export', [AdminBookingController::class, 'export'])->name('bookings.export');
    
    // Catamarans Management
    Route::resource('catamarans', AdminCatamaranController::class);
    Route::post('/catamarans/{catamaran}/toggle', [AdminCatamaranController::class, 'toggle'])->name('catamarans.toggle');
    Route::post('/catamarans/{catamaran}/images', [AdminCatamaranController::class, 'uploadImages'])->name('catamarans.images.upload');
    Route::delete('/catamarans/{catamaran}/images/{image}', [AdminCatamaranController::class, 'deleteImage'])->name('catamarans.images.delete');
    Route::post('/catamarans/{catamaran}/images/reorder', [AdminCatamaranController::class, 'reorderImages'])->name('catamarans.images.reorder');
    
    // Availability Management
    Route::get('/disponibilita', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::get('/disponibilita/{catamaran}', [AvailabilityController::class, 'calendar'])->name('availability.calendar');
    Route::post('/disponibilita/{catamaran}/update', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::post('/disponibilita/{catamaran}/block', [AvailabilityController::class, 'block'])->name('availability.block');
    Route::post('/disponibilita/{catamaran}/unblock', [AvailabilityController::class, 'unblock'])->name('availability.unblock');
    Route::post('/disponibilita/{catamaran}/bulk', [AvailabilityController::class, 'bulkUpdate'])->name('availability.bulk');
    
    // Addons Management
    Route::resource('addons', AddonController::class);
    Route::post('/addons/{addon}/toggle', [AddonController::class, 'toggle'])->name('addons.toggle');
    Route::post('/addons/reorder', [AddonController::class, 'reorder'])->name('addons.reorder');
    
    // Discount Codes Management
    Route::resource('discounts', DiscountController::class);
    Route::post('/discounts/{discount}/toggle', [DiscountController::class, 'toggle'])->name('discounts.toggle');
    
    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/bookings', [ReportController::class, 'bookings'])->name('reports.bookings');
    Route::get('/reports/occupancy', [ReportController::class, 'occupancy'])->name('reports.occupancy');
    Route::get('/reports/export', [ReportController::class, 'export'])->name('reports.export');
    
    // Payments
    Route::get('/payments', [AdminPaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/{payment}', [AdminPaymentController::class, 'show'])->name('payments.show');
    Route::post('/payments/{payment}/refund', [AdminPaymentController::class, 'refund'])->name('payments.refund');
    
    // Settings
    Route::get('/impostazioni', [SettingsController::class, 'index'])->name('settings');
    Route::post('/impostazioni', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/impostazioni/time-slots', [SettingsController::class, 'timeSlots'])->name('settings.timeslots');
    Route::post('/impostazioni/time-slots', [SettingsController::class, 'updateTimeSlots'])->name('settings.timeslots.update');
});

/*
|--------------------------------------------------------------------------
| Auth Routes (Laravel Breeze/Fortify)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
