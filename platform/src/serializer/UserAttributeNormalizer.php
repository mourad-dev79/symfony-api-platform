<?php


namespace App\serializer;


use App\Entity\User;
use phpDocumentor\Reflection\DocBlock\Serializer;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\CircularReferenceException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerAwareTrait;
use Symfony\Component\Serializer\SerializerInterface;

class UserAttributeNormalizer implements ContextAwareNormalizerInterface, SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    const USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED = 'USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED';


    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(isset($context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED])){
            return false;
        }

        return $data instanceof User;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        if($this->isUserHimself($object)){
            $context['groups'][] = 'get-owner';
        }
        return $this->passOn($object,$format,$context);
    }

    private function isUserHimself($object)
    {
        return $object->getUsername() === $this->tokenStorage->getToken()->getUsername();
    }

    private function passOn($object,$format,$context){
        if(!$this->serializer instanceof NormalizerInterface){
            throw new \LogicException(sprintf('cannot normalize object "%s" because the injected serialzer is not a normalizer',$object));

        }

        $context[self::USER_ATTRIBUTE_NORMALIZER_ALREADY_CALLED] = true;

        return $this->serializer->normalize($object,$format,$context);
    }


}