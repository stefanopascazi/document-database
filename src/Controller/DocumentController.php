<?php

namespace App\Controller;

use App\Entity\Document;
use App\Repository\DocumentRepository;
use App\Service\FileUploader;
use App\Factory\ResponseFactory;
use App\Form\DocumentType;
use App\Producer\ParseUploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Messenger\MessageBusInterface;

#[Route('/document', name: 'app_document_')]
class DocumentController extends AbstractController
{

    public function __construct(private ResponseFactory $responseFactory) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, DocumentRepository $documentRepository): Response
    {
        return $this->responseFactory->create([
            'data' => $documentRepository->findBy(
                [],
                ["id" => "DESC"], 
                $request->query->getInt("per_page", 10), 
                (($request->query->getInt("page", 1) -1) * $request->query->getInt("per_page", 10))
            )
        ], 200, [], ["document", "document_categories", "document_tags"]);
    }

    #[Route('/', name: 'new', methods: ['POST'])]
    public function new(Request $request, DocumentRepository $documentRepository, FileUploader $fileUploader): Response
    {
        $document = new Document();
        $form = $this->createForm(DocumentType::class, $document);
        $form->submit(
            json_decode($request->getContent(), true)
        );

        $documentRepository->add($document, true);

        return $this->responseFactory->create([
            "data" => $document
        ], 201, [], ["document", "document_categories", "document_tags"]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Document $document): Response
    {
        return $this->responseFactory->create([
            'data' => $document,
        ], 200, [], ["document", "document_categories", "document_tags"]);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(Request $request, Document $document, DocumentRepository $documentRepository, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(DocumentType::class, $document);
        $form->submit(
            json_decode($request->getContent(), true)
        );

        $documentRepository->add($document, true);

        return $this->responseFactory->create([
            "data" => $document
        ], 201, [], ["document", "document_categories", "document_tags"]);
    }

    #[Route('/uploads/{id}', name: 'uploads', methods: ['POST'])]
    public function upload(Request $request, Document $document, DocumentRepository $documentRepository, FileUploader $fileUploader, MessageBusInterface $bus): Response
    {
        $upload = $request->files->get("file");
        if( $upload )
        {
            $results = $fileUploader->upload($upload);
            list($filename, $extension) = $results;
            $document->setFilepath($this->getParameter("file_directory"));
            $document->setExtension($extension);
            $document->setFilename($filename);
            $document->setParsed(0);

            $result = $documentRepository->getDocumentContentByProductId($document->getId());
            if( count($result) > 0 )
            {
                foreach( $result as $entity )
                {
                    $document->removeContent($entity);
                }
            }

            $documentRepository->add($document, true);

            // send to queue
            $bus->dispatch( new ParseUploadedFile($document->getId()) );
        }

        return $this->responseFactory->create([
            "data" => $document
        ], 201, [], ["document", "document_categories", "document_tags"]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Document $document, DocumentRepository $documentRepository): Response
    {
        if( $document->getFilename() != null )
        {
            unlink(
                $document->getFilepath() . "/" . $document->getFilename()
            );

            $result = $documentRepository->getDocumentContentByProductId($document->getId());
            if( count($result) > 0 )
            {
                foreach( $result as $entity )
                {
                    $document->removeContent($entity);
                }
            }
        }
        
        $documentRepository->remove($document, true);

        return $this->redirectToRoute('app_document_index', [], Response::HTTP_SEE_OTHER);
    }
}
