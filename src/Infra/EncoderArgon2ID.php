<?php 
namespace Cart\Infra;

use Cart\Model\Encoder;
use Exception;

class EncoderArgon2ID implements Encoder
{
    /**
     * Codifica a senha
     *
     * @param string $password
     * @return string
     */
    public function encode(string $password): string
    {
        $password = trim($password);

        if(isset(password_get_info($password)['algo'])) {
            return $password;
        }

        if(strlen($password) < 6) {
            throw new Exception('A senha deve ter no mínimo 6 caracteres');
        }

        return password_hash($password, PASSWORD_ARGON2ID);
    }

    /**
     * Decodifica a senha
     *
     * @param string $password
     * @param string $hash
     * @return boolean
     */
    public function decode(string $password, string $hash): bool
    {
        return password_verify(trim($password), trim($hash));
    }
}