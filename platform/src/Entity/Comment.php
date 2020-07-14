<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;



/**
 * @ApiResource(
 *     attributes={"order" = {"publishedAt": "DESC"} , "pagination_client_enabled"=true ,
 *                  "pagination_client_items_per_page", "pagination_partial"=true },
 *      itemOperations={
 *     "Get",
 *     "PUT" = {
 *      "access-control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY') and object.getAuthor()==user "
 *     }
 *     },
 *      collectionOperations={
 *     "Get",
 *     "Post"= {
 *      "access-control"="is_granted('IS_AUTHENTICATED_ANONYMOUSLY')"
 *     },
 *     "api_blog_posts_comments_get_subresource"={
 *      "normalization_context"={
 *     "groups"={"Get-comments-with-author"}
 *     }
 *     }
 *     },
 *     denormalizationContext={
 *     "groups"={"post"}
 *     }
 *     )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 */
class Comment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"post","Get-comments-with-author"})
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post","Get-comments-with-author"})
     */
    private $content;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
     * @Groups({"Get-comments-with-author"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\BlogPost", inversedBy="comments")
     */
    private $blogpost;

    public function __construct()
    {
        $this->setPublishedAt(new \DateTime());
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTimeInterface $publishedAt): self
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getBlogpost(): ?BlogPost
    {
        return $this->blogpost;
    }

    public function setBlogpost(?BlogPost $blogpost): self
    {
        $this->blogpost = $blogpost;

        return $this;
    }

    public function __toString()
    {
        return substr($this->content ,0,20).'...';
    }

}
