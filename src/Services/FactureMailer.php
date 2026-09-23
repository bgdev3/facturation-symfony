<?php
namespace App\Services;

use App\Entity\Devis;
use App\Entity\Facture;
use App\Services\PdfGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class FactureMailer
{
    public function __construct(
        private readonly PdfGenerator $pdfGenerator, 
        private readonly MailerInterface $mailer
    ){}

    public function invoiceOnDevisAccept(Facture $facture): void
    {
        $pdfContent = $this->pdfGenerator->generateContent('pdf/invoice.html.twig', ['facture' => $facture]);

        $this->sendPdfMail(
            to: $facture->getClient()->getEmail(),
            subject: 'Facture n°' . $facture->getNumero(),
            text: 'Veuillez trouver ci-joint votre facture n°' . $facture->getNumero() . ' au format PDF.',
            pdfContent: $pdfContent,
            filename: 'facture.pdf',
        );
    }

    public function quotationOnDevisSend(Devis $devis): void
    {
        $pdfContent = $this->pdfGenerator->generateContent('pdf/quotation.html.twig', ['devis' => $devis]);

        $this->sendPdfMail(
            to: $devis->getClient()->getEmail(),
            subject: 'Votre devis n°' . $devis->getNumero(),
            text: 'Veuillez trouver ci-joint votre devis n°' . $devis->getNumero() . ' au format PDF.',
            pdfContent: $pdfContent,
            filename: 'devis.pdf',
        );
    }

    private function sendPdfMail(string $to, string $subject, string $text, string $pdfContent, string $filename): void
    {
        $mail = (new Email())
            ->from('support@demo.fr')
            ->to($to)
            ->subject($subject)
            ->text($text)
            ->attach($pdfContent, $filename, 'application/pdf');

        $this->mailer->send($mail);
    }
}