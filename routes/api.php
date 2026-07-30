<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\EvidenceController;
use App\Http\Controllers\Api\AssignmentController;
use App\Http\Controllers\Api\NoticeController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\CategoryController;


// TOURIST Dash
Route::group([], function () {
    
    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
        Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-reset-otp', [AuthController::class, 'verifyResetOtp']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::post('/contact-message', [ContactController::class, 'sendMessage']);
    Route::get('/notices', [NoticeController::class, 'getPublishedNotices']);
    Route::get('/public/reviews', [ReviewController::class, 'publicReviews']);
    Route::get('/categories', [CategoryController::class, 'index']);

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/submit-complaint', [ComplaintController::class, 'submitComplaint']);
        Route::post('/upload-evidence', [EvidenceController::class, 'uploadEvidence']);
        Route::get('/tourist-complaints/{touristID}', [ComplaintController::class, 'getTouristComplaints']);
        Route::put('/update-complaint/{id}', [ComplaintController::class, 'updateComplaint']);
        Route::delete('/tourist-delete-complaint/{id}', [ComplaintController::class, 'deleteTouristComplaint']);
        
        Route::get('/tourist-notifications/{touristID}', [NotificationController::class, 'getTouristNotifications']);
        Route::get('/tourist-notifications-count/{touristID}', [NotificationController::class, 'getTouristUnreadCount']);
        Route::patch('/tourist-notifications/read/{id}', [NotificationController::class, 'markTouristNotificationRead']);
        Route::patch('/tourist-notifications/read-all/{touristID}', [NotificationController::class, 'markAllTouristNotificationsRead']);
        
        Route::put('/update-tourist/{id}', [UserController::class, 'updateTourist']);
        
        Route::post('/reviews', [ReviewController::class, 'store']);
        Route::get('/reviews/complaint/{complaintID}', [ReviewController::class, 'getByComplaint']);
    });
});


// ADMIN Dash
Route::group([], function () {

    Route::middleware('throttle:5,1')->group(function () {
        Route::post('/login-user', [UserController::class, 'loginUser']);
        Route::post('/register-user', [UserController::class, 'registerUser']);
    });

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/assign-police', [AssignmentController::class, 'assignPolice']);
        Route::post('/remove-police-assignment', [AssignmentController::class, 'removePolice']);
        
        Route::get('/complaint/{id}', [ComplaintController::class, 'getComplaintById']);
        Route::delete('/delete-complaint/{id}', [ComplaintController::class, 'deleteComplaint']);
        Route::post('/reject-complaint/{id}', [ComplaintController::class, 'rejectComplaint']);
        
        Route::get('/all-users', [UserController::class, 'getAllUsers']);
        Route::put('/update-user/{id}', [UserController::class, 'updateUser']);
        Route::delete('/delete-user/{id}', [UserController::class, 'deleteUser']);
        
        Route::get('/all-tourists', [UserController::class, 'getAllTourists']);
        Route::delete('/delete-tourist/{id}', [UserController::class, 'deleteTourist']);
        
        Route::get('/admin/reviews', [ReviewController::class, 'index']);
        Route::get('/admin/reviews/{reviewID}', [ReviewController::class, 'show']);
        Route::put('/admin/reviews/{reviewID}/reject', [ReviewController::class, 'reject']);
        
        Route::get('/admin/categories', [CategoryController::class, 'adminIndex']);
        Route::post('/admin/categories', [CategoryController::class, 'store']);
        Route::put('/admin/categories/{id}', [CategoryController::class, 'update']);
        
        Route::get('/dashboard-stats', [ComplaintController::class, 'dashboardStats']);
        Route::get('/complaints-by-category', [ComplaintController::class, 'complaintsByCategory']);
        Route::get('/complaints-by-district', [ComplaintController::class, 'complaintsByDistrict']);
        Route::get('/recent-complaints', [ComplaintController::class, 'recentComplaints']);
    });
});


// POLICE Dash

Route::group([], function () {

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('/police-dashboard/{userID}', [AssignmentController::class, 'policeDashboardStats']);
        Route::get('/assigned-complaints/{userID}', [AssignmentController::class, 'getAssignedComplaints']);
        
        Route::patch('/update-complaint-status', [ComplaintController::class, 'updateStatus']);
        Route::patch('/save-police-note', [ComplaintController::class, 'savePoliceNote']);
        Route::patch('/update-police-password', [UserController::class, 'updatePolicePassword']);
        
        Route::get('/police/notices', [NoticeController::class, 'getAllNotices']);
        Route::post('/create-notice', [NoticeController::class, 'createNotice']);
        Route::put('/update-notice/{id}', [NoticeController::class, 'updateNotice']);
        Route::patch('/deactivate-notice/{id}', [NoticeController::class, 'deactivateNotice']);
        Route::patch('/republish-notice/{id}', [NoticeController::class, 'republishNotice']);
        Route::patch('/admin/notices/{id}/reject', [NoticeController::class, 'rejectNotice']);
        
        Route::get('/notifications/{userID}', [NotificationController::class, 'getNotifications']);
        Route::patch('/notifications/read/{id}', [NotificationController::class, 'markAsRead']);
    });
});

// SHARED ROUTES (Admin & Police)

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('/all-complaints', [ComplaintController::class, 'getAllComplaints']);
});
