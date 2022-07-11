<?php

namespace App\Entity;

use App\Repository\DocumentContentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentContentRepository::class)]
class DocumentContent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        "documentContent"
    ])]
    private $id;

    #[ORM\Column(type: 'text')]
    #[Groups([
        "documentContent",
        "tag_documentContents"
    ])]
    private $content;

    #[ORM\ManyToOne(targetEntity: Document::class, inversedBy: 'content')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        "documentContent_document"
    ])]
    private $document;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'documentContents')]
    private $tag;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'documentContents')]
    private $category;

    public function __construct()
    {
        $this->tag = new ArrayCollection();
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->document;
    }

    public function setDocument(?Document $document): self
    {
        $this->document = $document;

        return $this;
    }

    /**
     * @return Collection<int, Tag>
     */
    public function getTag(): Collection
    {
        return $this->tag;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tag->contains($tag)) {
            $this->tag[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tag->removeElement($tag);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }
}
