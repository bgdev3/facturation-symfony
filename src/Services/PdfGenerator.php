<?php
namespace App\Services;

use Sensiolabs\GotenbergBundle\GotenbergPdfInterface;
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
}
