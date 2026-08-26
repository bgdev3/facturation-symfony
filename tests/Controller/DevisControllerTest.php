<?php

namespace App\Tests\Controller;

use App\Entity\Devis;
use App\Entity\User;
use App\Enum\DevisStatut;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class DevisControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Devis> */
    private EntityRepository $deviRepository;
    private string $path = '/devis/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->deviRepository = $this->manager->getRepository(Devis::class);

        foreach ($this->deviRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Devis index');
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'devis[numero]' => 'DEV-2024-001',
            'devis[dateEmission]' => '2024-01-01',
            'devis[dateValidite]' => '2024-01-01',
            'devis[statut]' => DevisStatut::Brouillon->value,
            'devis[montantHT]' => '100.00',
            'devis[montantTVA]' => '20.00',
            'devis[montantTTC]' => '120.00',
        ]);

        self::assertResponseRedirects('/devis/');
        self::assertSame(1, $this->deviRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Devis();
        $fixture->setNumero('DEV-2024-001');
        $fixture->setDateEmission(new \DateTimeImmutable());
        $fixture->setDateValidite(new \DateTimeImmutable());
        $fixture->setStatut(DevisStatut::Brouillon);
        $fixture->setMontantHT('100.00');
        $fixture->setMontantTVA('20.00');
        $fixture->setMontantTTC('120.00');
    
        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Devi');
    }

    public function testEdit(): void
    {

        $client = new \App\Entity\Client();
        $client->setName('Client Test')
        ->setSiret('12345678901234')
        ->setEmail('client.test@example.com')
        ->setAddress('123 Rue Test')
        ->setPostalCode('12345')
        ->setCity('Testville')
        ->setPhone('0123456789')
        ->setCreatedAt(new \DateTimeImmutable());

        $user = $this->manager->getRepository(User::class)->findOneBy([]);

        if ($user === null) 
            $this->markTestSkipped('Aucun utilisateur en base pour créer le client de test.');
        
        $client->setUser($user);
        $this->manager->persist($client);

        $fixture = new Devis();
        $fixture->setNumero('DEV-2024-001');
        $fixture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $fixture->setDateValidite(new \DateTimeImmutable('2024-01-01'));
        $fixture->setStatut(DevisStatut::Brouillon);
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Mettre à jour', [
            'devis[numero]' => 'DEV-2024-001',
            'devis[dateEmission]' => '2024-02-02',
            'devis[dateValidite]' => '2024-02-02',
            'devis[statut]' => 'brouillon',
            'devis[montantHT]' => '100.00',
            'devis[montantTVA]' => '20.00',
            'devis[montantTTC]' => '120.00',
        ]);

        self::assertResponseRedirects('/devis/');

        $fixture = $this->deviRepository->findAll();

        self::assertSame('DEV-2024-001', $fixture[0]->getNumero());
        self::assertSame('2024-02-02', $fixture[0]->getDateEmission()->format('Y-m-d'));
        self::assertSame('2024-02-02', $fixture[0]->getDateValidite()->format('Y-m-d'));
        self::assertSame('brouillon', $fixture[0]->getStatut()->value);
        self::assertSame('100.00', $fixture[0]->getMontantHT());
        self::assertSame('20.00', $fixture[0]->getMontantTVA());
        self::assertSame('120.00', $fixture[0]->getMontantTTC());
    }

    public function testRemove(): void
    {
        $fixture = new Devis();
        $fixture->setNumero('DEV-2024-001');
        $fixture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $fixture->setDateValidite(new \DateTimeImmutable('2024-01-01'));
        $fixture->setStatut(DevisStatut::Brouillon);
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/devis/');
        self::assertSame(0, $this->deviRepository->count([]));
    }
}
