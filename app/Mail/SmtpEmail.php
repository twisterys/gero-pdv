<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class SmtpEmail extends Mailable
{
    public $emailSubject;
    public $emailBody;
    public $emailAttachments;

    public function __construct($subject, $body, array $attachmentsData = [])
    {
        $this->emailSubject = $subject;
        $this->emailBody = $body;
        $this->emailAttachments = $attachmentsData;
    }

    public function build()
    {
        $email = $this->subject($this->emailSubject)
        ->html(html_entity_decode($this->emailBody));

        foreach ($this->emailAttachments as $attachment) {
            if (is_array($attachment) && isset($attachment['data'], $attachment['name'])) {

                $attachmentData = $attachment['data'];

                if (!empty($attachment['is_base64'])) {
                    $attachmentData = base64_decode($attachmentData);
                }

                $email->attachData(
                    $attachmentData,
                    $attachment['name'],
                    $attachment['options'] ?? []
                );
            }
        }
        return $email;
    }
}
