<?php

namespace App\Service;

use DateTimeImmutable;

class JWTService
{
    //on génère le token $validity = 10800 = 3h
    
    /**
     * Génération du JWT
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 10800): string
    {
        // verifie que validity +petit ou = car elle ne peut pas etre inf à o
        if ($validity  > 0) {
            // temps now
            $now = new DateTimeImmutable();
            // recup timestamp de now + validity = date d'expiration
            $exp = $now->getTimestamp() + $validity;
            
            // c'est maintenant + exp
            $payload['iat'] = $now->getTimestamp();
            $payload['exp'] = $exp;
        
        }


        //on encode en base64 json
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));

        // on nettoie les valeurs encondées (retrait des +, / et =)
        $base64Header = str_replace(['+', '/', '='], ['-', '-', ''], $base64Header);
        $base64Payload = str_replace(['+', '/', '='], ['-', '-', ''], $base64Payload);

        // on génère la signature
        $secret = base64_encode($secret);

        $signature = hash_hmac('sha256', $base64Header . '.' . $base64Payload, $secret, true);

        $base64Signature = base64_encode($signature);

        $base64Signature = str_replace(['+', '/', '='], ['-', '-', ''], $base64Signature);

        //on crée le token

        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;

        return $jwt;
    }

    //on verifie que le token est valide (crrectement former)

    public function isValid(string $token): bool
    {
        return preg_match(
            '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
            $token
        ) === 1;
        
    }

    
    //on récupère le payload pour savoir si expirer
    public function getPayload(string $token): array
    {
       // On démonte le token
        $array = explode('.', $token);

        // On décode le payload
        $payload = json_decode(base64_decode($array[1]), true);
        
        return $payload;
        
    }
    
    //on récupère le header même methode que ci-dessus mais pour le header
    public function getHeader(string $token): array
    {
       // On démonte le token
        $array = explode('.', $token);

        // On décode le header
        $header = json_decode(base64_decode($array[0]), true);
        
        return $header;
        
    }

    
    
    //on verifie si token a expiré
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $now = new DateTimeImmutable();
    
        return $payload['exp'] < $now->getTimestamp();
        
    }
    
    //on verifie la signature du token 
    // on doit recuperer le header au lieu du payload 
    public function check(string $token, string $secret): bool
    {
        // on récup le header et le payload
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);

        //Regénérer un token de verification
        $verifToken = $this->generate($header, $payload, $secret, 0);

        
    
        return $token === $verifToken;
        
    }
}