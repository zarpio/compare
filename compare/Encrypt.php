<?php

/**
 * Class Encrypt
 *
 * @taken-from
 * http://stackoverflow.com/questions/1391132/two-way-encryption-in-php
 * http://blog.turret.io/the-missing-php-aes-encryption-example/ ( worked for me )
 */
class Encrypt
{
//    const ENCRYPTION_METHOD = "AES-256-CBC";
//
//    private $key;
//
//    public function __construct($key)
//    {
//        $this->key = $key;
//    }
//
//    public function encrypt($plainText)
//    {
//        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//
//        return openssl_encrypt($plainText, self::ENCRYPTION_METHOD, $this->key, 0, $iv);
//    }
//
//    public function decrypt($decryptedText)
//    {
//
//        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
//        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//
//        $data = $iv.$decryptedText;
//
//        $iv = substr($data, 0, $iv_size);
//
//
//        return openssl_decrypt(substr($data, $iv_size), self::ENCRYPTION_METHOD, $this->key, 0, $iv);
//    }


    const ENCRYPTION_METHOD = "AES-256-CBC";

    private $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    public function encrypt($plainText)
    {
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length(self::ENCRYPTION_METHOD));

        $encrypted = openssl_encrypt($plainText, self::ENCRYPTION_METHOD, $this->key, 0, $iv);

        $encrypted = $encrypted . ':' . $iv;

        return $encrypted;
    }

    public function decrypt($encrypted)
    {
        $parts = explode(':', $encrypted);

        $decrypted = openssl_decrypt($parts[0], self::ENCRYPTION_METHOD, $this->key, 0, $parts[1]);

        return $decrypted;
    }



}