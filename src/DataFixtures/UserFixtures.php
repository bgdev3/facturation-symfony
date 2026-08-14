<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    public const USER_REFERENCE = 'main-user';

    public function __construct(private UserPasswordHasherInterface $hasher){}

    public function load(ObjectManager $manager): void
    {
        $user = new User();

        $user->setUserName('Admin')
        ->setRoles(['ROLE_ADMIN'])
        ->setPassword($this->hasher->hashPassword($user, '0000'))
        ->setEmail('admin@test.fr')
        ->setIsVerified(true)
        ->setCompany($this->getReference(CompanyFixtures::COMPANY_REFERENCE, Company::class));

        $manager->persist($user);
        $manager->flush();

        $this->addReference(self::USER_REFERENCE, $user);
    }

     public function getDependencies(): array
     {
        return [
            CompanyFixtures::class,
        ];
     }
}

