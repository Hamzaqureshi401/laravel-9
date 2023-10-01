<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailObject;
use App\Mail\EmailNotification;



class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $mail;

    public function __construct(MailObject $mail)
    {
        $this->mail = $mail;
    }

    public function handle()
    {
        Mail::to($this->mail->to)
        ->send(new EmailNotification($this->mail->subject, $this->mail->body));
    }
}

