<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InterviewScheduleMail extends Mailable
{
    use SerializesModels;

    public $applicant;
    public $schedule;

    public function __construct($applicant, $schedule)
    {
        $this->applicant = $applicant;
        $this->schedule = $schedule;
    }

    public function build()
    {
        return $this->subject('Interview Schedule Notification')
                    ->view('emails.interview_schedule');
    }
}