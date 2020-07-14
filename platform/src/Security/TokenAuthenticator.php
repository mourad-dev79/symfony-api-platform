<?php


namespace App\Security;


use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Security\Guard\JWTTokenAuthenticator;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TokenAuthenticator extends JWTTokenAuthenticator
{

    public function getUser($preAuthToken, UserProviderInterface $userProvider)
    {
        $user =  parent::getUser($preAuthToken, $userProvider);

        if($user->getPasswordChangeDate() && $preAuthToken->getpayload()['iat'] < $user->getPasswordChangeDate()){
            throw new ExpiredTokenException();
        }
        
        return $user;
    }


}