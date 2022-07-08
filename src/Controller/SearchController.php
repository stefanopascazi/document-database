<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use App\Factory\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use FOS\ElasticaBundle\Finder\TransformedFinder;
use Elastica\Util;


#[Route('/search', name: 'app_search_')]
class SearchController extends AbstractController
{
    public function __construct( private ResponseFactory $responseFactory)
    {
        
    }

    #[Route('/document', name: 'document', methods: ["GET"])]
    public function document( Request $request, TransformedFinder $documentFinder ): Response
    {
        $data = [];
        $documents = [];
        $total_document_finder = "";
        $total_page = "";
        
        if( $request->query->get("search") != null )
        {
            $search = Util::escapeTerm($request->query->get("search"));

            $paginator = $documentFinder->findHybridPaginated($search)
                ->setCurrentPage($request->query->getInt("page", 1))
                ->setMaxPerPage($request->query->getInt("per_page", 10));
            $results = $paginator->getCurrentPageResults();

            $total_document_finder = $paginator->getNbResults();
            $total_page = $paginator->getNbPages();

            $data = array_map(fn($v) => $v->getResult()->getHit(), (array)$results);
            $documents = array_map(fn($v) => $v->getTransformed(), (array)$results);
            //dump($results);
        }

        return $this->responseFactory->create([
            'index' => $data,
            "data" => $documents,
        ], 200, [
            "total_data_finded" => $total_document_finder,
            "total_page" => $total_page,
        ], ["documentContent", "documentContent_document", "documentContent_document_tags", "documentContent_document_categories"]);
    }
}
