<?php


namespace App\Controller;


use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ResetPasswordAction
{
    private $validator;
    private $manager;
    private $tokenManager;

    private $passwordEncoder;

    public function __construct(ValidatorInterface $validator,UserPasswordEncoderInterface $passwordEncoder,
                                EntityManagerInterface $manager , JWTTokenManagerInterface $tokenManager)
    {
        $this->validator = $validator;
        $this->manager = $manager;
        $this->tokenManager = $tokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function __invoke(User $data)
    {
        $this->validator->validate($data);
        $data->setPassword($this->passwordEncoder->encodePassword($data,$data->getNewPassword()));
        $data->setPasswordChangeDate(time());
        $this->manager->flush();
        $token = $this->tokenManager->create($data);

        return new JsonResponse(['token'=>$token]);
    }

}