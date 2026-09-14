<?php

namespace App\EventSubscriber;

use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Enum\FactureStatut;
use App\Event\DevisAccepteEvent;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CreationFactureSubscriber implements EventSubscriberInterface
{

    public function __construct( private EntityManagerInterface $em, private NumberGenerator $numeroGenerator) {}

    public function onDevisAccepte(DevisAccepteEvent $event): void
    {
        $devis = $event->getDevis();

        $facture = new Facture();
      
        $facture->setNumero($this->numeroGenerator->genererProchainNumeroFacture())
            ->setDateEmission(\DateTimeImmutable::createFromMutable(new \DateTime()))
            ->setDateEcheance(\DateTimeImmutable::createFromMutable(new \DateTime('+30 days')))
            ->setStatut(FactureStatut::Brouillon)
            ->setClient($devis->getClient()) 
            ->setConditionsPaiement('Comptant')
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
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DevisAccepteEvent::class => 'onDevisAccepte',
        ];
    }
}
