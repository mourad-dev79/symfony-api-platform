<?php


namespace App\Controller;


use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UploadImageAction
{

    private $entityManager;
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * UploadImageAction constructor.
     * @param FormFactory $factory
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {

        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function __invoke(Request $request,FormFactory $factory)
    {
        $image = new Image();

        $form = $factory->create(ImageType::class,$image);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->entityManager->persist($image);
            $this->entityManager->flush();

            $image->setFile(null);
            return $image;
        }

        throw new ValidatorException(
            $this->validator->validate($image)
        );

    }
}