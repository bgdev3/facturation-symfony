<?php

namespace App\EventListener;

use App\Entity\User;
use App\Event\DevisSendEvent;
use App\Message\PdfGeneratorQuotationMessage;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEventListener]
final class DevisMailerListener
{
    public function __construct(private MessageBusInterface $bus,  private readonly Security $security) {}

    public function __invoke(DevisSendEvent $event): void
    {
        $user = $this->security->getUser();

        if (!$user instanceof User) {
            throw new \LogicException('Aucun utilisateur connecté pour générer le devis.');
        }

        $devis = $event->getDevis();
        $company = $user->getCompany();
       
        $this->bus->dispatch(new PdfGeneratorQuotationMessage($devis->getId(), $company->getId()));
    }
}
