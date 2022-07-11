<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        "category",
        "documentContent_document_categories",
        "document_categories"
    ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "category",
        "documentContent_document_categories",
        "document_categories"
    ])]
    private $title;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'category')]
    #[Groups([
        "category_categories",
    ])]
    private $category;

    #[ORM\ManyToMany(targetEntity: Document::class, mappedBy: 'category')]
    #[Groups([
        "category_documents"
    ])]
    private $documents;

    #[ORM\ManyToMany(targetEntity: DocumentContent::class, mappedBy: 'category')]
    private $documentContents;

    public function __construct()
    {
        $this->category = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->documentContents = new ArrayCollection();
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

    public function getCategory() : ?ArrayCollection
    {
        return $this->category;
    }

    public function setCategory(?self $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function addCategory(self $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
            $category->setCategory($this);
        }

        return $this;
    }

    public function removeCategory(self $category): self
    {
        if ($this->category->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getCategory() === $this) {
                $category->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Document>
     */
    public function getDocuments(): Collection
    {
        return $this->documents;
    }

    public function addDocument(Document $document): self
    {
        if (!$this->documents->contains($document)) {
            $this->documents[] = $document;
            $document->addCategory($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            $document->removeCategory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, DocumentContent>
     */
    public function getDocumentContents(): Collection
    {
        return $this->documentContents;
    }

    public function addDocumentContent(DocumentContent $documentContent): self
    {
        if (!$this->documentContents->contains($documentContent)) {
            $this->documentContents[] = $documentContent;
            $documentContent->addCategory($this);
        }

        return $this;
    }

    public function removeDocumentContent(DocumentContent $documentContent): self
    {
        if ($this->documentContents->removeElement($documentContent)) {
            $documentContent->removeCategory($this);
        }

        return $this;
    }
}
