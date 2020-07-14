<?php


namespace App\Security;


class TokenGenerator
{
    private const  ALPHABET = 'AZERTYUIOPQSDFGHJKLMWXCVBNazertyuiopqsdfghjkmnbvcwx1234567890';
    public function getRandomSecureToken(int $length = 30):string {
        $token ='';
        $maxNumber=strlen(self::ALPHABET);

        for($i=0 ; $i < $length ; $i++){
            $token.=self::ALPHABET[random_int(0 , $maxNumber - 1)];
        }
        return $token;
    }
}