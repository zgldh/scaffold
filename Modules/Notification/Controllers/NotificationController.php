<?php namespace Modules\Notification\Controllers;

use Illuminate\Http\Request;
use Modules\Notification\Repositories\NotificationRepository;
use App\Http\Requests\IndexRequest;
use App\Http\Requests\ShowRequest;
use Illuminate\Http\JsonResponse;
use App\Scaffold\AppBaseController;
use Modules\Notification\Requests\ReadNotificationRequest;

class NotificationController extends AppBaseController
{

    public function __construct(NotificationRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware("auth:api");
    }

    /**
     * Display a listing of the Notification.
     *
     * @no-permission
     * @param  IndexRequest $request
     * @return JsonResponse
     */
    public function index(IndexRequest $request)
    {
        $user = $request->user();
        $pageSize = 20;
        $page = $request->get('page', 1);
        list($items, $unreadCount) = $this->repository->getItems($user, $page, $pageSize);
        return $this->sendResponse([
            'items'  => $items,
            'unread' => $unreadCount
        ], '');
    }

    /**
     * Display the specified Notification.
     *
     * @no-permission
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function show($id, ShowRequest $request)
    {
        $user = $request->user();
        $item = $user->notifications()->find($id);
        if (empty($item)) {
            return $this->sendError('Notification not found');
        }
        $item->load($request->getWith());

        return $this->sendResponse($item, '');
    }

    /**
     * Read a notification
     *
     * @no-permission
     * @param   int $id
     * @param   ReadNotificationRequest $request
     * @return  JsonResponse
     */
    public function read($id, ReadNotificationRequest $request)
    {
        $user = $request->user();
        $item = $user->notifications()->find($id);
        if (empty($item)) {
            return $this->sendError('Notification not found');
        }
        $item->markAsRead();

        return $this->sendResponse($item, '');
    }

    /**
     * Read all notification
     *
     * @no-permission
     * @return  JsonResponse
     */
    public function readAll(Request $request)
    {
        $user = $request->user();
        $user->unreadNotifications()->update(['read_at' => now()]);
        return $this->sendResponse(true, '');
    }

    /**
     * Unread a notification
     *
     * @no-permission
     * @param      $id
     * @param     ReadNotificationRequest $request
     * @return    JsonResponse
     */
    public function unread($id, ReadNotificationRequest $request)
    {
        $user = $request->user();
        $item = $user->notifications()->find($id);
        if (empty($item)) {
            return $this->sendError('Notification not found');
        }
        $item->markAsUnread();

        return $this->sendResponse($item, '');
    }

    /**
     * Remove the specified Notification from storage.
     *
     * @no-permission
     * @param    int $id
     *
     * @return  JsonResponse
     */
    public function destroy($id, Request $request)
    {
        $user = $request->user();
        $item = $user->notifications()->find($id);
        if (empty($item)) {
            return $this->sendError('Notification not found');
        }

        $item->delete();

        return $this->sendResponse($item, 'Notification deleted successfully.');
    }

    /**
     * Read latest notifications after specific created at time
     *
     * @no-permission
     * @param      \Modules\Notification\Requests\GetReadLatestRequest $request
     * @return    JsonResponse
     */
    public function getReadLatest($lastCreatedAt, \Modules\Notification\Requests\GetReadLatestRequest $request)
    {
        $user = $request->user();
        list($items, $unreadCount) = $this->repository->getLatest($user, $lastCreatedAt);
        return $this->sendResponse([
            'items'  => $items,
            'unread' => $unreadCount
        ], '');
    }
}
