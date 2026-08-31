<?php

namespace App\Controller\Pdf;

use App\Entity\Devis;
use App\Entity\Facture;
use App\Services\PdfGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PdfController extends AbstractController
{
    #[Route('/pdf/facture/{id}', name: 'pdf.facture')]
    public function facture(PdfGenerator $pdfGenerator, Facture $facture): Response
    {
        return $pdfGenerator->generate('pdf/invoice.html.twig', ['facture' => $facture]);
    }

    #[Route('/pdf/devis/{id}', name: 'pdf.devis')]
    public function devis(PdfGenerator $pdfGenerator, Devis $devis): Response
    {
        return $pdfGenerator->generate('pdf/quotation.html.twig', ['devis' => $devis]);
    }
}
