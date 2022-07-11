<?php

namespace App\Entity;

use App\Repository\TagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TagRepository::class)]
class Tag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        "tag",
        "documentContent_document_tags",
        "document_tags"
    ])]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups([
        "tag",
        "documentContent_document_tags",
        "document_tags"
    ])]
    private $title;

    #[ORM\ManyToMany(targetEntity: Document::class, mappedBy: 'tag')]
    #[Groups([
        "tag_documents"
    ])]
    private $documents;

    #[ORM\ManyToMany(targetEntity: DocumentContent::class, mappedBy: 'tag')]
    #[Groups([
        "tag_documentContents"
    ])]
    private $documentContents;

    public function __construct()
    {
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
            $document->addTag($this);
        }

        return $this;
    }

    public function removeDocument(Document $document): self
    {
        if ($this->documents->removeElement($document)) {
            $document->removeTag($this);
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
            $documentContent->addTag($this);
        }

        return $this;
    }

    public function removeDocumentContent(DocumentContent $documentContent): self
    {
        if ($this->documentContents->removeElement($documentContent)) {
            $documentContent->removeTag($this);
        }

        return $this;
    }
}
