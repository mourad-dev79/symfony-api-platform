<?php


namespace App\Email;




use App\Entity\User;
use Symfony\Bundle\TwigBundle\DependencyInjection\Compiler\TwigEnvironmentPass;
use Symfony\Bundle\TwigBundle\DependencyInjection\TwigExtension;
use Twig\Environment;

class Mailer
{

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    private $twig;

    public function __construct(\Swift_Mailer $mailer , Environment $twig)
    {

        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user){
        $body = $this->twig->render(
            'email/confirmation.html.twig',[
                'user'=>$user
            ]
        );

        $message = (new \Swift_Message('confirmation email address'))
            ->setFrom('mouradslimani27@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body,'text/html');
        $this->mailer->send($message);
    }
}