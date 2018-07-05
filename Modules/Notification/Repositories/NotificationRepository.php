<?php namespace Modules\Notification\Repositories;

use Modules\Notification\Models\Notification;
use App\Scaffold\BaseRepository;
use Modules\User\Models\User;

class NotificationRepository extends BaseRepository
{
    /**
     * @var  array
     */
    protected $fieldSearchable = [
        "type",
        "data",
        "read_at"
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Notification::class;
    }

    /**
     * @no-permission
     * @param $notifiableUser
     * @param $lastCreatedAt
     * @return array
     */
    public function getLatest($notifiableUser, $lastCreatedAt)
    {
        $items = $notifiableUser->notifications()->where('created_at', '>', $lastCreatedAt)->get();
        $items = $items->toArray();

        $items = $this->decorateWithNotifiers($items);

        $unreadCount = $notifiableUser->unreadNotifications()->count();
        return [$items, $unreadCount];
    }

    /**
     * @no-permission
     * @param $notifiableUser
     * @param int $page
     * @param int $pageSize
     * @return array
     */
    public function getItems($notifiableUser, $page = 1, $pageSize = 20)
    {
        $items = $notifiableUser->notifications()->skip(($page - 1) * $pageSize)->take($pageSize)->get();
        $items = $items->toArray();

        $items = $this->decorateWithNotifiers($items);

        $unreadCount = $notifiableUser->unreadNotifications()->count();
        return [$items, $unreadCount];
    }

    private function decorateWithNotifiers($notifications)
    {
        $result = [];
        $notifiers = $this->getNotifiers($notifications);
        foreach ($notifications as $notification) {
            $notifier = @$notifiers[$notification['data']['notifier_id']] ?: null;
            $notification['data']['notifier'] = $notifier;
            $result[] = $notification;
        }
        return $result;
    }

    private function getNotifiers($notifications)
    {
        $notifierIds = array_map(function ($item) {
            return $item['data']['notifier_id'];
        }, $notifications);
        $notifiers = User::whereIn('id', $notifierIds)->get();
        $notifiers = $notifiers->keyBy('id');
        return $notifiers;
    }
}
