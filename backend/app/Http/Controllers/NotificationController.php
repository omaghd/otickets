<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(protected NotificationService $service)
    {
    }

    public function index(): JsonResponse
    {
        $notifications = request('unread') == 'true'
            ? $this->service->getUnread(auth('sanctum')->user())
            : $this->service->getAll(auth('sanctum')->user());

        return $this->successResponse(data: $notifications);
    }

    public function getCounts(): JsonResponse
    {
        $counts = $this->service->getCounts(auth('sanctum')->user());

        return $this->successResponse(data: $counts);
    }

    public function markAllAsRead(): JsonResponse
    {
        $this->service->markAllAsRead(auth('sanctum')->user());

        return $this->successResponse('All notifications marked as read');
    }

    public function markAsRead($notificationId): JsonResponse
    {
        try {
            $notification = $this->service->find($notificationId);

            $this->service->markAsRead($notification);

            return $this->successResponse('Notification marked as read');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('The notification does not exist', 404);
        }
    }

    public function markAsUnread($notificationId): JsonResponse
    {
        try {
            $notification = $this->service->find($notificationId);

            $notification->markAsUnread();

            return $this->successResponse('Notification marked as unread');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('The notification does not exist', 404);
        }
    }

    public function destroy($notificationId): JsonResponse
    {
        try {
            $notification = $this->service->find($notificationId);

            $this->service->delete($notification);

            return $this->successResponse('Notification deleted');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('The notification does not exist', 404);
        }
    }
}
