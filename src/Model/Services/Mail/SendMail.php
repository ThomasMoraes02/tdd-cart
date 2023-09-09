<?php 
namespace Cart\Model\Services\Mail;

use Cart\Model\ValueObjects\Email;

interface SendMail
{
    public function send(Email $to, string $subject, string $message): void;
}