<?php

namespace App\Notifications\User;

use Illuminate\Bus\Queueable;
use App\Notifications\MainNotification;

class UserBlocked extends MainNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $data = array(
            "user_id" => $notifiable->id,
            "user_type" => "user",
            "email_data" => $this->dataArray($notifiable)
        );
        return $this->sendMail('account_blocked', 'Account Blocked', $notifiable->id, $data);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return $this->dataArray($notifiable);
    }

    private function dataArray($notifiable){
        return [
            'name' => $notifiable->complete_name,
            //these 4 below should always be there otherwise error will occur also if added new type below than add that in Helpers_dianuj.php in 'notification_colors' function's array to support it properly.
            'route' => 'front.user.my_profile',
            'notify_title' => 'Account Blocked',
            'notify_type' => 'account_blocked',
            'msg' =>  'Your account is blocked.',
        ];
    }
}
