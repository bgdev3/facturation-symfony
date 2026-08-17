<?php

namespace App\DataFixtures;

use App\Entity\Devis;
use App\Entity\Facture;
use App\Entity\LigneFacture;
use App\Enum\DevisStatut;
use App\Enum\FactureStatut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class FactureFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
      
        $faker = Factory::create('fr_FR');

        $listDevis = $manager->getRepository(Devis::class)->findAll();

       foreach($listDevis as $devis) {

            if ($devis->getStatut() != DevisStatut::Accepte) 
                continue;
            
            $totalHT = 0;
            $totalTVA = 0;

            $facture = new Facture();
            $facture->setNumero('FAC-' . $faker->unique()->numerify('####'))
                ->setDateEmission(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-3 months', 'now')))
                ->setDateEcheance(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('now', '+1 month')))
                ->setStatut($faker->randomElement(FactureStatut::cases()))
                ->setClient($devis->getClient()) // même client que le devis
                ->setConditionsPaiement($faker->randomElement(['Comptant', '30 jours net', '30 jours fin de mois']))
                ->setDevis($devis); // lien vers le devis d'origine

            $nbLignes = $faker->numberBetween(2, 5);
            for ($d = 0; $d < $nbLignes; $d++) {

                $quantite = $faker->numberBetween(1, 10);
                $prixUnitaire = $faker->randomFloat(2, 10, 100);
                $tauxTVA = 20.00;

                $ligne = new LigneFacture();
                $ligne->setDesignation($faker->sentence())
                    ->setQuantite($quantite)
                    ->setPrixUnitaireHT($prixUnitaire)
                    ->setTauxTVA((string) $tauxTVA)
                    ->setFacture($facture);

                $montantLigneHT = round($quantite * $prixUnitaire, 2);
                $totalHT += $montantLigneHT;
                $totalTVA += round($montantLigneHT * ($tauxTVA / 100), 2);

                $manager->persist($ligne);
            }

            $facture->setMontantHT((string) round($totalHT, 2));
            $facture->setMontantTVA((string) round($totalTVA, 2));
            $facture->setMontantTTC((string) round($totalHT + $totalTVA, 2));

            $manager->persist($facture);

       }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [DevisFixtures::class];
    }
}
