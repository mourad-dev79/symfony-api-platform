<?php

namespace App\EventSubscriber;
use App\Entity\User;
use ApiPlatform\Core\EventListener\EventPriorities;
use App\Security\TokenGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserRegisterSubscriber implements EventSubscriberInterface
{
	private $passwordEncoder;
    private $generator;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder , TokenGenerator $generator){
		$this->passwordEncoder = $passwordEncoder;
        $this->generator = $generator;
    }

	public static function getSubscribedEvents(){
		return [
			KernelEvents::VIEW =>['userRegistred', EventPriorities::PRE_WRITE]
		];
	}


	public function userRegistred(ViewEvent $event){

		$user = $event->getControllerResult();
		$method = $event->getRequest()->getMethod();

		if(!$user instanceof User || !in_array($method , [Request::METHOD_POST , Request::METHOD_PUT])){
			return ;
		}

		$user->setPassword($this->passwordEncoder->encodePassword($user,$user->getPassword()));

		$user->setConfirmationToken($this->generator->getRandomSecureToken());
	}
}



