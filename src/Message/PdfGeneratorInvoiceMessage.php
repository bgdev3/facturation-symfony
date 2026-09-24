<?php
 namespace App\Message;

 use Symfony\Component\Messenger\Attribute\AsMessage;

 #[AsMessage('async')]
class PdfGeneratorInvoiceMessage
{
    public function __construct(
        public readonly int $id, 
        public readonly int $companyId
    ) {}
}