<?php

namespace App\Security;

use Firebase\JWT\JWT as Firebase;
use Firebase\JWT\Key;

class JWT
{

    public function __construct(private $jwt_secret)
    {     
    }

    public function encode( $user )
    {
        if (is_null($user))
            return null;

        $payload = [
            'iss'  => $_SERVER['SERVER_NAME'],
            'aud'  => 1,
            'iat'  => time(),
            'nbf'  => time(),
            'exp'  => time() + 2592000,
            'data' => $user,
        ];

        return Firebase::encode($payload, $this->jwt_secret, 'HS256');
    }


    public function decode($jwt)
    {
        return Firebase::decode($jwt, new Key($this->jwt_secret, 'HS256'));
    }

}