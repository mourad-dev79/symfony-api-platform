<?php


namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminSecuityController extends AbstractController
{
    /**
     * @Route("/login" , name="security_login")
     */

    public function login()
    {
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout" , name="security_logout")
     */
    public function logout()
    {

    }

}