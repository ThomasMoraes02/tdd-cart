<?php 
namespace Cart\Infra\Services;

use DomainException;
use Cart\Model\ValueObjects\Email;
use Cart\Model\Services\Mail\SendMail;

class Mail implements SendMail
{
    /**
     * Envia um e-mail
     *
     * @param Email $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    public function send(Email $to, string $subject, string $message): void
    {
        mail($to, $subject, $message);
    }
}