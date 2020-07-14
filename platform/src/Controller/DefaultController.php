<?php

namespace App\Controller;

use App\Security\UserConfirmationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="default")
     */
    public function index()
    {
        return $this->render('base.html.twig');
    }

    /**
     * @Route("/confirm-user/{token}" , name="confirmation-email")
     */

    public function confirmUser(string $token, UserConfirmationService $service){
        $service->ConfirmeUser($token);

        return $this->redirectToRoute('default');
    }
}
