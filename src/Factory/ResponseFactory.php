<?php

namespace App\Factory;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ResponseFactory
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function create( object | array $data, $status = 200, $headers = [], $groups = [] ) : Response
    {
        return new Response(
            $this->serializer->serialize(
                $data, 
                JsonEncoder::FORMAT, 
                [AbstractNormalizer::GROUPS => $groups]
            ),
            $status,
            array_merge($headers, ["Content-Type", "application/json;charset=UTF-8"])
        );
    }
}