<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Factory\ResponseFactory;
use App\Factory\CreateElasticaQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Finder\TransformedFinder;

#[Route('/search', name: 'app_search_')]
class SearchController extends AbstractController
{
    public function __construct( 
        private ResponseFactory $responseFactory,
        private CreateElasticaQuery $createElasticaQuery)
    {
        
    }

    #[Route('/document', name: 'document', methods: ["GET"])]
    public function document( Request $request, TransformedFinder $documentFinder ): Response
    {
        list($data, $documents, $total_document_finder, $total_page) = $this->createElasticaQuery->getResults($request, $documentFinder);

        return $this->responseFactory->create([
            'index' => $data,
            "data" => $documents,
        ], 200, [
            "total_data_finded" => $total_document_finder,
            "total_page" => $total_page,
        ], ["documentContent", "documentContent_document", "documentContent_document_tags", "documentContent_document_categories"]);
    }
}
