<?php
namespace App\Services;

use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
use Sensiolabs\GotenbergBundle\Processor\InMemoryProcessor;
use Symfony\Component\HttpFoundation\Response;

final class PdfGenerator
{
    public function __construct(private readonly GotenbergPdfInterface $gotenberg){}
  
    public function generate(string $template, array $context): Response
    {
        return $this->gotenberg->html()
            ->content($template, $context)
            ->generate()
            ->stream() 
        ;
    }

    // Pourpièce jointe, contenu brut, pas de flux de sortie
    public function generateContent(string $template, array $context): string
    {
        return $this->gotenberg->html()
            ->content($template, $context)
            ->processor(new InMemoryProcessor())
            ->generate()
            ->process();
        ;
    }
}
