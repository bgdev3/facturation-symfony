<?php

namespace App\EventListener;

use App\Event\DevisSendEvent;
use App\Services\FactureMailer;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

#[AsEventListener]
final class DevisMailerListener
{
    public function __construct(private readonly FactureMailer $mailer) {}

    public function __invoke(DevisSendEvent $event): void
    {
        $devis = $event->getDevis();
        $this->mailer->quotationOnDevisSend($devis);
    }
}
