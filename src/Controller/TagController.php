<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\TagRepository;
use App\Repository\DocumentRepository;
use App\Repository\DocumentContentRepository;
use App\Factory\ResponseFactory;
use App\Form\TagType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/tag', name: 'app_tag_')]
class TagController extends AbstractController
{

    public function __construct(private ResponseFactory $responseFactory) {}

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, TagRepository $tagRepository): Response
    {
        return $this->responseFactory->create([
            'data' => $tagRepository->findBy(
                [],
                ["id" => "DESC"], 
                $request->query->getInt("per_page", 10), 
                (($request->query->getInt("page", 1) -1) * $request->query->getInt("per_page", 10))
            )
        ], 200, [], ["tag", "tag_documents"]);
    }

    #[Route('/', name: 'new', methods: ['POST'])]
    public function new(Request $request, TagRepository $tagRepository, DocumentRepository $documentRepository, DocumentContentRepository $documentContentRepository ): Response
    {
        $tag = new Tag();
        $form = json_decode($request->getContent(), true);

        $tag->setTitle($form['title']);
        if( array_key_exists( "document", $form ) )
        {
            $document = $documentRepository->find($form['document']['id']);
            $tag->addDocument($document);

            $contents = $documentContentRepository->findBy(["document" => $document]);
            foreach( $contents as $content )
            {
                $tag->addDocumentContent($content);
            }
        }

        $tagRepository->add($tag, true);

        return $this->responseFactory->create([
            "data" => $tag
        ], 201, [], ["tag", "tag_documents" ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Tag $tag): Response
    {
        return $this->responseFactory->create([
            'data' => $tag,
        ], 200, [], ["tag", "tag_documents"]);
    }

    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(Request $request, Tag $tag, TagRepository $tagRepository, DocumentRepository $documentRepository): Response
    {
        $form = json_decode($request->getContent(), true);
        
        array_key_exists( "title", $form ) && $tag->setTitle($form['title']);

        if( array_key_exists( "document", $form ) )
        {
            $document = $documentRepository->find($form['document']['id']);
            $tag->addDocument($document);
        }

        $tagRepository->add($tag, true);

        return $this->responseFactory->create([
            "data" => $tag
        ], 201, [], ["tag", "tag_documents"]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Tag $tag, TagRepository $tagRepository): Response
    {        
        $tagRepository->remove($tag, true);

        return $this->redirectToRoute('app_tag_index', [], Response::HTTP_SEE_OTHER);
    }
}
