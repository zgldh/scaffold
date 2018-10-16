<?php namespace Tests\Modules\Notification\Notification;

use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Notifications\Notification;
use Tests\TestCase;
use Modules\User\Models\User;

class BaseTest extends TestCase
{
    use DatabaseTransactions;

    protected function sendNotification($user)
    {
        $notification = new TestNotification();
        $user->notify($notification);
        return $notification;
    }
}

class TestNotification extends Notification
{
    use Queueable;

    private $notifier = null;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $notifier = null)
    {
        //
        $this->notifier = $notifier;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
//    public function toMail($notifiable)
//    {
//        return (new MailMessage)->markdown('Modules\Notification::bar');
//    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'url_title'   => 'You got a TestNotification',
            'url'         => '/dashboard',
            'notifier_id' => $this->notifier ? $this->notifier->id : null, //user ID, null is system
            'content'     => '<p>TestNotification <b>content</b></p>'
        ];
    }
}
