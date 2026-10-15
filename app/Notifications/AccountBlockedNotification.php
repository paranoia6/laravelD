<?php

namespace App\Notifications;

use App\Models\Account;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountBlockedNotification extends Notification
{
    use Queueable;


    public function __construct(
        public Account $account,
        public bool $under72Hours,
        public int $refundAmount
    ) {
    }


    public function via(object $notifiable): array
    {
        return [
            'database',
        ];
    }


    public function toArray(
        object $notifiable
    ): array {

        return [

            'type' =>
                'account_blocked',

            'account_id' =>
                $this->account->id,

            'username' =>
                $this->account->username,

            'under_72_hours' =>
                $this->under72Hours,

            'refund_amount' =>
                $this->refundAmount,

            'message' =>
                $this->under72Hours
                    ? "اکانت {$this->account->username} مسدود شد و مبلغ "
                    . number_format(
                        $this->refundAmount
                    )
                    . ' تومان برگشت داده شد.'
                    : "اکانت {$this->account->username} مسدود شد و به دلیل گذشت بیش از ۷۲ ساعت مبلغی برگشت داده نشد.",
        ];
    }
}
