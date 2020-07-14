<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiSubresource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BlogPostRepository")
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "id":"exact",
 *          "title": "partial",
 *          "content": "partial",
 *          "author" : "exact",
 *          "author.name" : "partial"
 *     }
 * )
 * @ApiFilter(
 *     DateFilter::class,
 *     properties={
 *       "publishedat"
 *     }
 * )
 * @ApiFilter(RangeFilter::class , properties={"id"})
 * @ApiFilter(OrderFilter::class , properties={"id" , "publishedat" , "title"} , arguments={"orderParameterName"= "_order"})
 * @ApiResource(
 *      itemOperations={
 *     "Get",
 *     "PUT" = {
 *      "access-control"="is_granted('IS_AUTHENTICATED_FULLY')"
 *     }},
 *      collectionOperations={
 *     "Get"= {
 *      "access-control"="is_granted('IS_AUTHENTICATED_FULLY')"
 * },
 *     "post"={
 *       "denormalization_context"={
 *     "groups"={"post"}
 *     }}
 * }
 * )
 */
class BlogPost
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank()
     * @Groups({"post"})
     */
    private $title;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type(type="\DateTime")
     */
    private $publishedat;

    /**
     * @ORM\Column(type="text",nullable=true)
     * @Groups({"post"})
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255 , nullable=true)
     * @Groups({"post"})
     */
    private $slug;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="blogposts")
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="blogpost")
     * @ApiSubresource()
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Image", inversedBy="blogPosts")
     * @ApiSubresource()
     * @Groups({"post"})
     */
    private $images;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getPublishedat(): ?\DateTimeInterface
    {
        return  $this->publishedat;
    }

    public function setPublishedat(?\DateTimeInterface $publishedat): self
    {
        $this->publishedat = $publishedat;

        return $this;
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


    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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
            $comment->setBlogpost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->contains($comment)) {
            $this->comments->removeElement($comment);
            // set the owning side to null (unless already changed)
            if ($comment->getBlogpost() === $this) {
                $comment->setBlogpost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Image[]
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Image $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
        }

        return $this;
    }

    public function removeImage(Image $image): self
    {
        if ($this->images->contains($image)) {
            $this->images->removeElement($image);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->title;

    }
}
