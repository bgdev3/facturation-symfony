<?php
namespace App\Tests\Controller;

use App\Entity\Company;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CompanyControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class);

        // Nettoie les données de test résiduelles d'un précédent run
        $existingUser = $this->em->getRepository(User::class)->findOneBy(['username' => 'testuserCompany']);
        if ($existingUser) {
            $this->em->remove($existingUser);
        }

        foreach ($this->em->getRepository(Company::class)->findAll() as $company) {
            $this->em->remove($company);
        }

        $this->em->flush();
    }

    public function testIndex(): void
    {
        $passwordHasher = static::getContainer()->get(UserPasswordHasherInterface::class);

        $company = new Company();
        $company->setName('Test Company')
            ->setAddress('123 Test St')
            ->setPostalCode('12345')
            ->setCity('Test City')
            ->setSiret('12345678901234')
            ->setTvaIntraCom('FR12345678901')
            ->setIban('FR7612345678901234567890123')
            ->setBic('ABCDEFGH');

        $user = new User();
        $user->setUsername('testuserCompany')
            ->setEmail('test@example.com')
            ->setRoles(['ROLE_USER'])
            ->setPassword($passwordHasher->hashPassword($user, 'password'))
            ->setIsVerified(true);

        $this->em->persist($company);
        $this->em->persist($user);
        $this->em->flush();

        $this->client->loginUser($user);
        $this->client->request('GET', '/company');

        self::assertResponseIsSuccessful();
    }
}