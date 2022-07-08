<?php

namespace App\Consumer;

use App\Producer\ParseUploadedFile;
use App\Repository\DocumentRepository;
use App\Entity\DocumentContent;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

#[AsMessageHandler]
class ParseUploadedConsumer
{

    private $documentRepository;
    private $managerRegistry;

    public function __construct( DocumentRepository $documentRepository, ManagerRegistry $managerRegistry )
    {
        $this->documentRepository = $documentRepository;
        $this->managerRegistry = $managerRegistry;
    }

    public function __invoke( ParseUploadedFile $parseUploadedFile )
    {
        $document = $this->documentRepository->find( $parseUploadedFile->getId() );

        if( !$document )
        {
            return;
        }

        $process = Process::fromShellCommandline("docker run --rm -i -v {$document->getFilepath()}:/files stedotdev/textractor -i {$document->getFilename()}");
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        //$document->setContent( );
        $document->setParsed(1);
        $content = str_replace(["\n", "\r", "  "], " ", $process->getOutput());

        $entityManager = $this->managerRegistry->getManager();
        
        if( $document->getExtension() != 'pdf' )
        {
            $content = preg_replace( '~((?:\S*?\s){400})~', "$1\f", $content );
        }

        $pages = explode("\f", $content);
        foreach( $pages as $page )
        {
            $documentContent = new DocumentContent();
            $documentContent->setContent($page);
            $documentContent->setDocument($document);

            $entityManager->persist($documentContent);
        }

        

        $entityManager->flush();

        $this->documentRepository->add($document, true);

        return $process->stop();
    }

}