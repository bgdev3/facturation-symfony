<?php

namespace App\EventSubscriber;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Entity\User;
use App\Enum\ConditionsStatus;
use App\Enum\FactureStatut;
use App\Event\DevisAccepteEvent;
use App\Message\PdfGeneratorInvoiceMessage;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class CreationFactureSubscriber implements EventSubscriberInterface
{

    public function __construct( 
        private readonly EntityManagerInterface $em, 
        private readonly NumberGenerator $numeroGenerator, 
        private readonly MessageBusInterface $bus, 
        private readonly Security $security
        ) {}

    public function onDevisAccepte(DevisAccepteEvent $event): void
    {
        $devis = $event->getDevis();

        $facture = new Facture();
      
        $facture->setNumero($this->numeroGenerator->genererProchainNumeroFacture())
            ->setDateEmission(\DateTimeImmutable::createFromMutable(new \DateTime()))
            ->setDateEcheance(\DateTimeImmutable::createFromMutable(new \DateTime('+30 days')))
            ->setStatut(FactureStatut::Brouillon)
            ->setClient($devis->getClient()) 
            ->setConditionsPaiement(ConditionsStatus::Paiments_30_jours)
            ->setDevis($devis); 
            
        foreach ($devis->getLigneDevis() as $ligneDevis) {
            $ligneFacture = new LigneFacture();
            $ligneFacture->setDesignation($ligneDevis->getDesignation());
            $ligneFacture->setQuantite($ligneDevis->getQuantite());
            $ligneFacture->setPrixUnitaireHT($ligneDevis->getPrixUnitaireHT());
            $ligneFacture->setTauxTVA($ligneDevis->getTauxTVA());
            $facture->addLigneFacture($ligneFacture);

            $this->em->persist($ligneFacture);
        }

        $facture->setMontantHT($devis->getMontantHT())
            ->setMontantTVA($devis->getMontantTVA())
            ->setMontantTTC($devis->getMontantTTC());

        $this->em->persist($facture);
        $this->em->flush();

        // Récupère le User afin de récupere l'Id de la company.
        $user = $this->security->getUser();
        if (!$user instanceof User)
            throw new \LogicException('Aucun utilisateur connecté pour générer le devis.');
        
        $company = $user->getCompany();
       
        $this->bus->dispatch(new PdfGeneratorInvoiceMessage($facture->getId(), $company->getId()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DevisAccepteEvent::class => 'onDevisAccepte',
        ];
    }
}
