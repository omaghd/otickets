<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CannedResponseController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TicketAgentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/**
 * Protected routes
 */
Route::middleware(['auth:sanctum'])
    ->group(function () {
        Route::controller(AuthController::class)
            ->group(function () {
                Route::get('user', 'user')
                    ->name('get-user');
                Route::post('logout', 'logout')
                    ->name('logout');
                Route::put('update-profile', 'updateProfile')
                    ->name('update-profile');
                Route::put('update-password', 'updatePassword')
                    ->name('update-password');
            });

        Route::middleware(['can:is-admin'])->group(function () {
            Route::apiResource('departments', DepartmentController::class)
                ->except(['show']);

            Route::prefix('departments')
                ->controller(DepartmentController::class)
                ->group(function () {
                    Route::delete('{id}/force-delete', 'forceDelete');
                    Route::put('{id}/restore', 'restore');
                });

            Route::apiResource('users', UserController::class)
                ->except(['index', 'show']);

            Route::prefix('users')
                ->controller(UserController::class)
                ->group(function () {
                    Route::get('clients-stats', 'getClientsStats');
                    Route::get('agents-response-time', 'getAgentsResponseTime');
                    Route::get('tickets-counts-by-agent-and-priority', 'countRelatedTicketsByAgentAndPriority');
                    Route::delete('{id}/force-delete', 'forceDelete');
                    Route::put('{id}/restore', 'restore');
                });

            Route::apiResource('clients', ClientController::class)
                ->except(['show', 'index']);

            Route::prefix('clients')
                ->controller(ClientController::class)
                ->group(function () {
                    Route::delete('{id}/force-delete', 'forceDelete');
                    Route::put('{id}/restore', 'restore');
                });

            Route::apiResource('categories', CategoryController::class)
                ->except(['show', 'index']);

            Route::prefix('categories')
                ->controller(CategoryController::class)
                ->group(function () {
                    Route::delete('{id}/force-delete', 'forceDelete');
                    Route::put('{id}/restore', 'restore');
                });

            Route::apiResource('faqs', FaqController::class)
                ->except(['show', 'index']);

            Route::prefix('faqs')
                ->controller(FaqController::class)
                ->group(function () {
                    Route::delete('{id}/force-delete', 'forceDelete');
                    Route::put('{id}/restore', 'restore');
                });

            Route::apiResource('newsletters', NewsletterController::class)
                ->only(['index', 'update', 'destroy']);
        });

        Route::apiResource('users', UserController::class)
            ->only(['index']);

        Route::apiResource('clients', ClientController::class)
            ->only(['index']);

        Route::apiResource('tickets', TicketController::class)
            ->except(['show']);

        Route::get('categories/tickets-counts', [CategoryController::class, 'countRelatedTickets']);

        Route::prefix('tickets')
            ->controller(TicketController::class)
            ->group(function () {
                Route::get('stats', 'getStats');
                Route::get('counts-by-status', 'countByStatus');
                Route::get('{reference}', 'show');
                Route::delete('{id}/force-delete', 'forceDelete');
                Route::put('{id}/restore', 'restore');
            });

        Route::put('assign-agent', TicketAgentController::class)
            ->name('assign-agent');

        Route::apiResource('canned-responses', CannedResponseController::class)
            ->except(['show']);

        Route::prefix('canned-responses')
            ->controller(CannedResponseController::class)
            ->group(function () {
                Route::delete('{id}/force-delete', 'forceDelete');
                Route::put('{id}/restore', 'restore');
            });

        Route::post('ticket-replies', TicketReplyController::class)
            ->name('ticket-replies.store');

        Route::prefix('notifications')
            ->controller(NotificationController::class)
            ->group(function () {
                Route::get('/', 'index');
                Route::get('counts', 'getCounts');
                Route::put('{notificationId}/mark-as-read', 'markAsRead');
                Route::put('mark-all-as-read', 'markAllAsRead');
                Route::put('{notificationId}/mark-as-unread', 'markAsUnread');
                Route::delete('{notificationId}', 'destroy');
            });
    });

/**
 * Public routes
 */
Route::prefix('categories')
    ->controller(CategoryController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{slug}', 'show');
    });

Route::prefix('faqs')
    ->controller(FaqController::class)
    ->group(function () {
        Route::get('/', 'index');
        Route::get('{slug}', 'show');
        Route::get('suggestions/{query}', 'getSuggestions');
    });

Route::controller(AuthController::class)
    ->group(function () {
        Route::post('login', 'login')
            ->name('login');
        Route::post('register', 'register')
            ->name('register');
        Route::post('forgot-password', 'forgotPassword')
            ->name('forgot-password');
        Route::post('reset-password', 'resetPassword')
            ->name('reset-password');
    });

Route::post('newsletters', [NewsletterController::class, 'store']);
