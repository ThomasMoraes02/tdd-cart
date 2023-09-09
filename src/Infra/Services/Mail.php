<?php 
namespace Cart\Infra\Services;

use Cart\Model\ValueObjects\Email;
use Cart\Model\Services\Email\SendMail;
use DomainException;

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
        $success = mail($to, $subject, $message);

        if (!$success) {
            throw new DomainException('Não foi possível enviar o e-mail.');
        }
    }
}