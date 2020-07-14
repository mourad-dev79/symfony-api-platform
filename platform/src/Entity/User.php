<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use App\Controller\ResetPasswordAction;

/**
 * @ApiResource(
 *      itemOperations={
 *     "Get"={
 *      "access-control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')",
 *      "normalization_context"={
 *          "groups"={"Get"}
 *     }
 *     },
 *     "Put"={
 *     "access-control"="is_granted('IS_AUTHENTICATED_FULLY') and obeject==user",
 *      "denormalization_context"={
 *          "groups"={"Put"}
 *     },
 *     "normalization_context"={
 *          "groups"={"Get"}
 *          }
 *     },
 *
 *      "Put-reset-Password"={
 *      "access-control"="is_granted('IS_AUTHENTICATED_FULLY') and obeject==user",
 *      "method"="PUT",
 *      "path"="/users/{id}/reset-password",
 *      "controller"=ResetPasswordAction::class,
 *      "denormalization_context"={
 *          "groups"={"Put-reset-Password"}
 *     }
 *     }
 *     },
 *      collectionOperations={
 *     "Post"={
 *       "denormalization_context"={
 *     "groups"={"Post"}
 *     },
 *     "normalization_context"={"groups"={"Get"}}
 *     }
 *     }
 * )
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
class User implements UserInterface 
{
    const ROLE_COMENTATOR = 'ROLE_COMENTATOR';
    const ROLE_WRITER = 'ROLE_WRITER';
    const ROLE_EDITOR = 'ROLE_EDITOR';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPERADMIN = 'ROLE_SUPERADMIN';

    const DEFAULT_ROLES = [self::ROLE_COMENTATOR];
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"Get"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"Get","Post","Get-comments-with-author"})
     * @Assert\NotBlank(groups={"Post","Put"})
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"Post"})
     */
    private $password;

    /**
    * @Assert\NotBlank(groups={"Post"})
     * @Groups({"Post"})
     * @Assert\Expression(
     *     "this.getPassword() === this.getRetypedPassword()",
     *      message="password dosen't match",
     *     groups={"post"}
     * )
    */
    private $retypedPassword;

    /**
     * @Groups({"Put-reset-Password"})
     * @Assert\NotBlank(groups={"Put-reset-Password"})
     */
    private $newPassword;

    /**
     * @Groups({"Put-reset-Password"})
     * @Assert\Expression(
     *     "this.getNewPassword() === this.getNewRetypedPassword()",
     *      message="password dosen't match",
     *     groups={"Put-reset-Password"}
     * )
     */
    private $newRetypedPassword;

    /**
     * @Groups({"Put-reset-Password"})
     * @Assert\NotBlank(groups={"Put-reset-Password"})
     * @UserPassword(groups={"Put-reset-Password"})
     */
    private $oldPassword;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank(groups={"Post"})
     * @Groups({"Put","Post","get-admin","get-owner"})
     * @Assert\Email(groups={"Post","Put"})
     */


    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"Get","Post","Put","Get-comments-with-author"})
     * @Assert\NotBlank(groups={"Post","Put"})
     * @Assert\Length(min=6 , max=255 , groups={"Post","Put"})
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BlogPost", mappedBy="author")
     * @Groups({"read"})
     */
    private $blogposts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="author")
     * @Groups({"read"})
     */
    private $comments;

    /**
     * @ORM\Column(type="simple_array", length=255, nullable=true)
     * @Groups({"get_admin","get-owner"})
     */
    private $roles;

    /**
     * @ORM\Column(type="integer" , nullable=true)
     */
    private $passwordChangeDate;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string" , length=255 , nullable=true)
     */
    private $confirmationToken;

    public function __construct()
    {

        $this->comments = new ArrayCollection();
        $this->blogposts = new ArrayCollection();
        $this->roles = self::DEFAULT_ROLES;
        $this->enabled = false;
        $this->confirmationToken = null;
    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
    public function getRoles(){
        return $this->roles;
    }

    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(){
        return Null;
    }

    public function eraseCredentials(){

    }

    /**
     * @return Collection|Blogpost[]
     */
    public function getBlogposts(): Collection
    {
        return $this->blogposts;
    }

    public function addBlogpost(Blogpost $blogpost): self
    {
        if (!$this->blogposts->contains($blogpost)) {
            $this->blogposts[] = $blogpost;
            $blogpost->setAuthor($this);
        }

        return $this;
    }

    public function removeBlogpost(Blogpost $blogpost): self
    {
        if ($this->blogposts->contains($blogpost)) {
            $this->blogposts->removeElement($blogpost);
            // set the owning side to null (unless already changed)
            if ($blogpost->getAuthor() === $this) {
                $blogpost->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRetypedPassword()
    {
        return $this->retypedPassword;
    }

    public function setRetypedPassword($retypedPassword): void
    {
        $this->retypedPassword = $retypedPassword;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword): void
    {
        $this->newPassword = $newPassword;
    }

    public function getNewRetypedPassword(): ?string
    {
        return $this->newRetypedPassword;
    }

    public function setNewRetypedPassword($newRetypedPassword): void
    {
        $this->newRetypedPassword = $newRetypedPassword;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword($oldPassword): void
    {
        $this->oldPassword = $oldPassword;
    }

    public function getPasswordChangeDate()
    {
        return $this->passwordChangeDate;
    }

    public function setPasswordChangeDate($passwordChangeDate): void
    {
        $this->passwordChangeDate = $passwordChangeDate;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @return null
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * @param null $confirmationToken
     */
    public function setConfirmationToken($confirmationToken): void
    {
        $this->confirmationToken = $confirmationToken;
    }

    public function __toString()
    {
        return $this->name;
    }


}

