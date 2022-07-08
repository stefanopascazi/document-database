<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use App\Repository\DocumentRepository;

#[AsCommand(
    name: 'textractor:generate',
    description: 'Add a short description for your command',
)]
class Textractor extends Command
{

    public function __construct(private KernelInterface $kernel, private DocumentRepository $documentRepository)
    {
        parent::__construct();
    }
    protected function configure(): void
    {
        // $this
        //     ->addArgument('filename', InputArgument::REQUIRED, 'File name is required')
        // ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $document = $this->documentRepository->findOneBy(["parsed" => 0]);

        if( $document )
        {
            $document->setParsed(1);

            if( $document->getFilename() )
            {

                $process = Process::fromShellCommandline("docker run --rm -i -v {$document->getFilepath()}:/files stedotdev/textractor -i {$document->getFilename()}");
                $process->run();
        
                // executes after the command finishes
                if (!$process->isSuccessful()) {
                    throw new ProcessFailedException($process);
                }
        
                $document->setContent($process->getOutput());
    
            }

            $this->documentRepository->add($document, true);
        }

        // $process = new Process([
        //     "/bin/bash",
        //     "docker run -ti -v {$document->getFilepath()}:/files stedotdev/textractor -i {$document->getFilename()}"
        // ]);

        return Command::SUCCESS;
    }
}
