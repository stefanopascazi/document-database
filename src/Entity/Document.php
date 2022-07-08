<?php

namespace App\Entity;

use App\Repository\DocumentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DocumentRepository::class)]
class Document
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $filepath;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $filename;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $extension;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $parsed = 0;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $ocr = 0;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $excerpt;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups([
        "document",
        "documentContent_document",
        "category_documents",
        "tag_documents"
    ])]
    private $description;

    #[ORM\OneToMany(mappedBy: 'document', targetEntity: DocumentContent::class, orphanRemoval: true)]
    #[Groups([
        "document_contents"
    ])]
    private $content;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'documents')]
    #[Groups([
        "documentContent_document_tags",
        "document_tags"
    ])]
    private $tag;

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'documents')]
    #[Groups([
        "documentContent_document_categories",
        "document_categories"
    ])]
    private $category;

    public function __construct()
    {
        $this->content = new ArrayCollection();
        $this->tag = new ArrayCollection();
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(string $filepath): self
    {
        $this->filepath = $filepath;

        return $this;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

        return $this;
    }

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getParsed(): ?int
    {
        return $this->parsed;
    }

    public function setParsed(int $parsed): self
    {
        $this->parsed = $parsed;

        return $this;
    }

    public function getOcr(): ?int
    {
        return $this->ocr;
    }

    public function setOcr(int $ocr): self
    {
        $this->ocr = $ocr;

        return $this;
    }

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): self
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function addContent(DocumentContent $content): self
    {
        if (!$this->content->contains($content)) {
            $this->content[] = $content;
            $content->setDocument($this);
        }

        return $this;
    }

    public function removeContent(DocumentContent $content): self
    {
        if ($this->content->removeElement($content)) {
            // set the owning side to null (unless already changed)
            if ($content->getDocument() === $this) {
                $content->setDocument(null);
            }
        }

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
