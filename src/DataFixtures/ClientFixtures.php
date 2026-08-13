<?php

namespace App\DataFixtures;

use App\Entity\Client;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        /** @var \App\Entity\User $user */
        $user = $this->getReference(UserFixtures::USER_REFERENCE, User::class);

        for ($i=0 ; $i <= 20; $i++) {

            $client = new Client();
            $client->setName($faker->company)
            ->setSiret($faker->siret)
            ->setAddress($faker->streetAddress)
            ->setPostalCode($faker->postcode)
            ->setCity($faker->city)
            ->setEmail($faker->companyEmail)
            ->setPhone($faker->phoneNumber)
            ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));

            $user->addClient($client);
            $manager->persist($client);
        }
        $manager->flush();
    }

    public function getDependencies(): array
     {
        return [
            UserFixtures::class,
        ];
     }
}
