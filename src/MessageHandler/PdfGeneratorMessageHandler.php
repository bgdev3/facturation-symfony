<?php

namespace App\MessageHandler;

use App\Message\PdfGeneratorInvoiceMessage;
use App\Message\PdfGeneratorQuotationMessage;
use App\Repository\CompanyRepository;
use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use App\Services\PdfGenerator;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\Exception\UnrecoverableMessageHandlingException;
use Symfony\Component\Mime\Email;

/**
 * C
 */
final class PdfGeneratorMessageHandler
{
    public function __construct(
        private readonly PdfGenerator $pdfGenerator,
        private readonly MailerInterface $mailer,
        private readonly DevisRepository $devisRepo,
        private readonly FactureRepository $factureRepo, 
        private readonly CompanyRepository $companyRepo
    ) {}

   #[AsMessageHandler]
    public function invoiceOnDevisAccept(PdfGeneratorInvoiceMessage $message): void
    {
        $facture = $this->factureRepo->find($message->id)
            ?? throw new UnrecoverableMessageHandlingException('Facture introuvable : ' . $message->id);

        $company = $this->companyRepo->findOneBy([])
            ?? throw new UnrecoverableMessageHandlingException('Company introuvable : ');


        $pdfContent = $this->pdfGenerator->generateContent('pdf/invoice.html.twig', ['facture' => $facture, 'company' => $company]);

        $this->sendPdfMail(
            to: $facture->getClient()->getEmail(),
            subject: 'Facture n°' . $facture->getNumero(),
            text: 'Veuillez trouver ci-joint votre facture n°' . $facture->getNumero() . ' au format PDF.',
            pdfContent: $pdfContent,
            filename: 'facture.pdf',
        );
    }

    #[AsMessageHandler]
    public function quotationOnDevisSend(PdfGeneratorQuotationMessage $message): void
    {
        $devis = $this->devisRepo->find($message->id) 
            ?? throw new UnrecoverableMessageHandlingException('Devis introuvable : ' . $message->id);

        $company = $this->companyRepo->findOneBy([])
            ?? throw new UnrecoverableMessageHandlingException('Company introuvable : ');

        $pdfContent = $this->pdfGenerator->generateContent('pdf/quotation.html.twig', ['devis' => $devis, 'company' => $company]);

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
