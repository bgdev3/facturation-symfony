<?php

namespace App\Tests\Controller;

use App\Entity\Devis;
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
        self::assertPageTitleContains('Devi index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'devi[numero]' => 'Testing',
            'devi[dateEmission]' => 'Testing',
            'devi[dateValidite]' => 'Testing',
            'devi[statut]' => 'Testing',
            'devi[montantHT]' => 'Testing',
            'devi[montantTVA]' => 'Testing',
            'devi[montantTTC]' => 'Testing',
            'devi[client]' => 'Testing',
        ]);

        self::assertResponseRedirects('/devis');

        self::assertSame(1, $this->deviRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Devis();
        $fixture->setNumero('My Title');
        $fixture->setDateEmission('My Title');
        $fixture->setDateValidite('My Title');
        $fixture->setStatut('My Title');
        $fixture->setMontantHT('My Title');
        $fixture->setMontantTVA('My Title');
        $fixture->setMontantTTC('My Title');
        $fixture->setClient('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Devi');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Devis();
        $fixture->setNumero('Value');
        $fixture->setDateEmission('Value');
        $fixture->setDateValidite('Value');
        $fixture->setStatut('Value');
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');
        $fixture->setClient('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'devi[numero]' => 'Something New',
            'devi[dateEmission]' => 'Something New',
            'devi[dateValidite]' => 'Something New',
            'devi[statut]' => 'Something New',
            'devi[montantHT]' => 'Something New',
            'devi[montantTVA]' => 'Something New',
            'devi[montantTTC]' => 'Something New',
            'devi[client]' => 'Something New',
        ]);

        self::assertResponseRedirects('/devis');

        $fixture = $this->deviRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumero());
        self::assertSame('Something New', $fixture[0]->getDateEmission());
        self::assertSame('Something New', $fixture[0]->getDateValidite());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getMontantHT());
        self::assertSame('Something New', $fixture[0]->getMontantTVA());
        self::assertSame('Something New', $fixture[0]->getMontantTTC());
        self::assertSame('Something New', $fixture[0]->getClient());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Devis();
        $fixture->setNumero('Value');
        $fixture->setDateEmission('Value');
        $fixture->setDateValidite('Value');
        $fixture->setStatut('Value');
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');
        $fixture->setClient('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/devis');
        self::assertSame(0, $this->deviRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
