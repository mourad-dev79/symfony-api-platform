<?php


namespace App\serializer;


use ApiPlatform\Core\Exception\RuntimeException;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserContextBuilder implements SerializerContextBuilderInterface
{

    private $decorated;
    private $authorizationChecker;

    public function __construct(SerializerContextBuilderInterface $decorated, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @inheritDoc
     */
    public function createFromRequest(Request $request, bool $normalization, array $extractedAttributes = null): array
    {
        $context = $this->decorated->createFromRequest($request,$normalization,$extractedAttributes);

        $resourceClasse = $context['resource_class'] ?? null ;

        if( User::class === $resourceClasse && isset($context['groups']) && $normalization == true && $this->authorizationChecker->isGranted(User::ROLE_ADMIN)){
            $context['groups'][] = 'get-admin';
        }
        return $context;
    }
}