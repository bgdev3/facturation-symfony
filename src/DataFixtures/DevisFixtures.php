<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\Devis;
use App\Entity\LigneDevis;
use App\Enum\DevisStatut;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class DevisFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
       
        $faker = Factory::create('fr_FR');
      

        // récupère les clients déjà créés (si DevisFixtures dépend de ClientFixtures)
        $clients = $manager->getRepository(Client::class)->findAll();

        for ($i = 0; $i < 20; $i++) {
            $totalHT = 0;
            $totalTVA = 0;

            $devis = new Devis();
            $devis->setNumero('DEV-' . $faker->unique()->numerify('####'))
            ->setDateEmission(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween('-6 months', 'now')))
            ->setDateValidite(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween('now', '+1 month') ))
            ->setStatut($faker->randomElement(DevisStatut::cases()))
            ->setClient($faker->randomElement($clients));

            $nbLignes = $faker->numberBetween(2, 5);

            for ( $d = 0; $d < $nbLignes; $d++)  {

                $quantite  = $faker->numberBetween(1, 10);
                $prixUnitaire = $faker->randomFloat(2, 10, 100);
                $tauxTVA = '20.00';

                $ligne = new LigneDevis();
                $ligne->setDesignation($faker->sentence())
                ->setQuantite($quantite)
                ->setPrixUnitaireHT($prixUnitaire)
                ->setTauxTVA((string) $tauxTVA)
                ->setDevis($devis);

                $montantLigneHT = round($quantite * $prixUnitaire, 2);
                $totalHT += $montantLigneHT;
                $totalTVA += round($montantLigneHT * ($tauxTVA / 100), 2);
                
                $manager->persist($ligne);
            }

            $devis->setMontantHT((string) round($totalHT, 2));
            $devis->setMontantTVA((string) round($totalTVA, 2));
            $devis->setMontantTTC((string) round($totalHT + $totalTVA, 2));

            $manager->persist($devis);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [ClientFixtures::class];
    }
}
