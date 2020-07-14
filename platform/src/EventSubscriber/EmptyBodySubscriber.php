<?php


namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Exception\EmptyBodyException;
use Egulias\EmailValidator\Validation\Exception\EmptyValidationList;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class EmptyBodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST=>['handleEmptyBody' , EventPriorities::POST_DESERIALIZE]
        ];
    }

    public function handleEmptyBody(RequestEvent $event){

        $method = $event->getRequest()->getMethod();
        $route = $event->getRequest()->get('_route');

        if(!in_array($method , [Request::METHOD_POST , Request::METHOD_PUT]) || substr($route,0,3) !== 'api'){
            return;
        }

        $data = $event->getRequest()->get('data');
        if(null  === $data){
            throw new EmptyBodyException();
        }

    }

}