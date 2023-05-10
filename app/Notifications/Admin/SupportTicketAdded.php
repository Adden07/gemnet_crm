<?php

namespace App\Notifications\Admin;

use Illuminate\Bus\Queueable;
use App\Notifications\MainNotification;

class SupportTicketAdded extends MainNotification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    private $ticket, $user, $department;

    public function __construct($ticket, $user, $department)
    {
        $this->ticket = $ticket;
        $this->user = $user;
        $this->department = $department;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toDatabase($notifiable){
        return $this->dataArray($notifiable);
    }

    private function dataArray($notifiable){
        return [
            'ticket' => $this->ticket,
            'id' => $this->ticket->hashid,
            'name' => $notifiable->complete_name,
            //these 4 below should always be there otherwise error will occur also if added new type below than add that in Helpers_dianuj.php in 'notification_colors' function's array to support it properly.
            'route' => 'admin.home',
            'notify_title' => 'New Support Ticket',
            'notify_type' => 'p1_dispatch_failed',
            'msg' => "New Support Ticket Added",
        ];
    }
}
