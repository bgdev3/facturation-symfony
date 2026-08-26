<?php

namespace App\DataFixtures;

use App\Entity\Facture;
use App\Entity\Paiement;
use App\Enum\PaiementStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class PaiementFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $factures = $manager->getRepository(Facture::class)->FindAll();

        foreach ($factures as $facture) {
            
            if ($faker->boolean(100)) {

                $paiement = new Paiement();
                $paiement->setFacture($facture)
                        ->setMontant($facture->getMontantTTC())
                        ->setDate(\DateTimeImmutable::createFromMutable( $faker->dateTimeBetween($facture->getDateEmission()->format('Y-m-d'), 'now')))
                        ->setMoyen($faker->randomElement(PaiementStatus::cases()));
                        
                $manager->persist($paiement);
            }
        }
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [FactureFixtures::class];
    }
}
