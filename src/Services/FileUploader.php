<?php

namespace App\Services;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Format;
use Intervention\Image\ImageManager; // Version 4
use League\Flysystem\Filesystem; 
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Service pour gérer les fichiers uploadés. Customisé pour utiliser Flysystem au lieu de déplacer les fichiers manuellement.
 */
class FileUploader
{
    public function __construct(
        private Filesystem  $uploadsStorage,
        private SluggerInterface $slugger,
    ) {}

    /**
     * Upload un fichier, le converti en webp avant upload  et retourne son nouveau nom.
     */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename     = $this->slugger->slug($originalFilename);
        $newFilename      = $safeFilename . '-' . uniqid() . '.webp';

        $manager =  ImageManager::usingDriver(Driver::class);
        $webpContent = $manager
            ->decode($file->getPathname())
            ->encodeUsingFormat(Format::WEBP)
            ->toString();
            
        // Utiliser Flysystem pour écrire le fichier dans le stockage configuré (local, S3, etc.)
        $this->uploadsStorage->write($newFilename, $webpContent);
    
        return $newFilename;
    }

    public function delete(string $filename): void
    {
        if ($this->uploadsStorage->fileExists($filename)) {
            $this->uploadsStorage->delete($filename); 
        }
    }
}