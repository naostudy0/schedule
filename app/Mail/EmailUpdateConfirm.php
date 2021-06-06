<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailUpdateConfirm extends Mailable
{
    use Queueable, SerializesModels;

    protected $email_update;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_update)
    {
        $this->email_update_token = $email_update->email_update_token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('メールアドレス変更確認')
            ->view('auth.email.email_update')
            ->with(['email_update_token' => $this->email_update_token]);
    }
}
