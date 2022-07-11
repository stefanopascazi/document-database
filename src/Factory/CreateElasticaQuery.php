<?php

namespace App\Factory;

use Elastica\Util;
use Elastica\Query\BoolQuery;
use Elastica\Query\Nested;
use Elastica\Query\MatchQuery;
use FOS\ElasticaBundle\Finder\TransformedFinder;

class CreateElasticaQuery
{
    private $bool;

    public function __construct() {
        $this->bool = new BoolQuery;
    }

    public function getBool()
    {
        return $this->bool;
    }

    public function addQuery( array $fields = [], string $text = "" ) : self
    {
        $search = Util::escapeTerm($text);

        // searchbale content
        foreach( $fields as $field )
        {
            $match = new MatchQuery();
            $match->setFieldQuery($field, $search);        
            $this->bool->addShould($match);
        }

        return $this;
    }

    public function addNested( string $path, string $field, string | int $value, bool $must = true ) : self
    {
        $nested = new Nested();
        $nested->setPath($path);

        $match = new MatchQuery();
        !is_numeric( $value) ? $match->setFieldQuery($field, Util::escapeTerm($value)) : $match->setFieldQuery($field, $value);
        $nested->setQuery($match);
        $must ? $this->bool->addMust($nested) : $this->bool->addShould($nested);

        return $this;
    }

    public function addNestedArray( mixed $params, bool $must = true ) : self
    {
        foreach( $params as $path => $arguments)
        {
            foreach( $arguments as $field => $value)
            {
                $this->addNested($path, $field, $value, $must);
            }
            
        }      

        return $this;
    }

    public function getResults( $request, $documentFinder )
    {
        $data = [];
        $documents = [];
        $total_document_finder = "";
        $total_page = "";
        
        if( $request->query->get("search") != null )
        {
            $this->addQuery(["content"], $request->query->get("search"))
            ->addNested("document", "document.title", $request->query->get("search"), false);
        }

        if( $request->query->all("filters") != null )
        {
            $this->addNestedArray($request->query->all("filters"));
        }

        $bool = $this->getBool();

        $paginator = $documentFinder->findHybridPaginated($bool)
            ->setCurrentPage($request->query->getInt("page", 1))
            ->setMaxPerPage($request->query->getInt("per_page", 10));

        $results = $paginator->getCurrentPageResults();

        $total_document_finder = $paginator->getNbResults();
        $total_page = $paginator->getNbPages();

        $data = array_map(fn($v) => $v->getResult()->getHit(), (array)$results);
        $documents = array_map(fn($v) => $v->getTransformed(), (array)$results);

        return [$data, $documents, $total_document_finder, $total_page];
    }
}