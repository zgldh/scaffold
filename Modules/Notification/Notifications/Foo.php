<?php

namespace Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\User\Models\User;

class Foo extends Notification
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)->markdown('Modules\Notification::foo');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'url_title'   => 'And this is a Foo',
            'url'         => '/user/role/list',
            'notifier_id' => $this->notifier ? $this->notifier->id : null, //user ID, null is system
            'content'     => '<p>Foo <span style="color: red">content</span></p>'
        ];
    }
}
