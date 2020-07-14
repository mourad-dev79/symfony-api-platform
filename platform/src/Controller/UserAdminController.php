<?php


namespace App\Controller;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\EasyAdminController as BaseAdminController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserAdminController extends BaseAdminController
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    protected function persistEntity($entity)
    {
        $this->UserEncodePassword($entity);
        parent::persistEntity($entity);
    }

    protected function updateEntity($entity)
    {
        $this->UserEncodePassword($entity);
        parent::persistEntity($entity);
    }

    /**
     * @param $entity
     */
    private function UserEncodePassword($entity): void
    {
        $entity->setPassword($this->passwordEncoder->encodePassword($entity, $entity->getPassword()));
    }


}