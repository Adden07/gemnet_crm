<?php

namespace App\Notifications;

use App\Helpers\CommonHelpers;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MainNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function sendMail($view, $subject, $user_id, $data, $attachment = null)
    {
        $email_data = $data['email_data'] ?? [];
        $data['subject'] = $subject;

        $sentEmail = CommonHelpers::save_email_to_db($data, $view, $email_data);

        $email_data['email_id'] = hashids_encode($sentEmail->id);

        // if($attachment){
        //     $attachment_arr = [
        //         'as' => 'filename.pdf',
        //         'mime' => 'text/pdf',
        //     ];
        //     $email = (new MailMessage())
        //         ->from(config('mail.from.address'), config('mail.from.name'))
        //         ->subject($subject)
        //         ->markdown('emails.' . $view, $data);

        //     foreach($attachment_arr as $arr){
        //         $email->attach(public_path($arr), [
        //             'as' => basename($arr),
        //             'mime' => 'text/pdf',
        //         ]);
        //     }
        //     dd($email);
        //     return $email;
        // }
        return (new MailMessage())
            ->from(config('mail.from.address'), config('app.name'))
            ->subject($subject)
            ->markdown('emails.'.$view, $data);
    }

    public function toDatabase($notifiable)
    {
        return [
            'user_id' => json_encode($this->violation->user_id),
            'complainer_id' => json_encode($this->violation->complainer_id),
            'msg' => $this->complainer." have reported against ".$this->complaint_against,
        ];
    }
}
