<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $targetDirectory;
    private $slugger;

    public function __construct($targetDirectory, SluggerInterface $slugger)
    {
        $this->targetDirectory = $targetDirectory;
        $this->slugger = $slugger;
    }

    public function upload( UploadedFile $file)
    {
        $orginalFileName = pathinfo(
            $file->getClientOriginalName(),
            PATHINFO_FILENAME
        );

        $safeFileName = $this->slugger->slug($orginalFileName);
        $extension = $file->guessClientExtension();

        $fileName = $safeFileName . '-' . uniqid() . '.' . $extension;

        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e )
        {
            return $e;
        }

        return [
            $fileName,
            $extension
        ];
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }
}