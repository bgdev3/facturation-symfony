<?php

namespace App\DataFixtures;

use App\Entity\Company;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CompanyFixtures extends Fixture
{
    public const COMPANY_REFERENCE = 'main-company';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');
        $company = new Company();
        $siren = substr($company->getSiret(), 0, 9);
      
        $company->setName($faker->company)
            ->setSiret($faker->siret) // 14 chiffres, Luhn valide
            ->setAddress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setTvaIntraCom('FR' . $faker->numerify('##') . $siren)  // TVA intracom FR : FR + 2 caractères (souvent chiffres) + SIREN (9 chiffres)
            ->setIban($faker->iban('FR'))
            ->setBic($faker->swiftBicNumber);

            // logo nullable, on laisse vide ou on set une url fake
            // $company->setLogo($faker->imageUrl());
        $manager->persist($company);
        $manager->flush();

        $this->addReference(self::COMPANY_REFERENCE, $company);
    }
}
