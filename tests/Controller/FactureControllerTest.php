<?php

namespace App\Tests\Controller;

use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class FactureControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<Facture> */
    private EntityRepository $factureRepository;
    private string $path = '/facture/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->factureRepository = $this->manager->getRepository(Facture::class);

        foreach ($this->factureRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Facture index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'facture[numero]' => 'Testing',
            'facture[deteEmission]' => 'Testing',
            'facture[dateEcheance]' => 'Testing',
            'facture[statut]' => 'Testing',
            'facture[montantHT]' => 'Testing',
            'facture[montantTVA]' => 'Testing',
            'facture[montantTTC]' => 'Testing',
            'facture[conditionsPaiment]' => 'Testing',
        ]);

        self::assertResponseRedirects('/facture');

        self::assertSame(1, $this->factureRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('My Title');
        $fixture->setDeteEmission('My Title');
        $fixture->setDateEcheance('My Title');
        $fixture->setStatut('My Title');
        $fixture->setMontantHT('My Title');
        $fixture->setMontantTVA('My Title');
        $fixture->setMontantTTC('My Title');
        $fixture->setConditionsPaiment('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Facture');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('Value');
        $fixture->setDeteEmission('Value');
        $fixture->setDateEcheance('Value');
        $fixture->setStatut('Value');
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');
        $fixture->setConditionsPaiment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'facture[numero]' => 'Something New',
            'facture[deteEmission]' => 'Something New',
            'facture[dateEcheance]' => 'Something New',
            'facture[statut]' => 'Something New',
            'facture[montantHT]' => 'Something New',
            'facture[montantTVA]' => 'Something New',
            'facture[montantTTC]' => 'Something New',
            'facture[conditionsPaiment]' => 'Something New',
        ]);

        self::assertResponseRedirects('/facture');

        $fixture = $this->factureRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getNumero());
        self::assertSame('Something New', $fixture[0]->getDeteEmission());
        self::assertSame('Something New', $fixture[0]->getDateEcheance());
        self::assertSame('Something New', $fixture[0]->getStatut());
        self::assertSame('Something New', $fixture[0]->getMontantHT());
        self::assertSame('Something New', $fixture[0]->getMontantTVA());
        self::assertSame('Something New', $fixture[0]->getMontantTTC());
        self::assertSame('Something New', $fixture[0]->getConditionsPaiment());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('Value');
        $fixture->setDeteEmission('Value');
        $fixture->setDateEcheance('Value');
        $fixture->setStatut('Value');
        $fixture->setMontantHT('Value');
        $fixture->setMontantTVA('Value');
        $fixture->setMontantTTC('Value');
        $fixture->setConditionsPaiment('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/facture');
        self::assertSame(0, $this->factureRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
