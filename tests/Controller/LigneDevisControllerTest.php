<?php

namespace App\Tests\Controller;

use App\Entity\LigneDevis;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LigneDevisControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<LigneDevis> */
    private EntityRepository $ligneDeviRepository;
    private string $path = '/ligne/devis/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->ligneDeviRepository = $this->manager->getRepository(LigneDevis::class);

        foreach ($this->ligneDeviRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('LigneDevi index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ligne_devi[designation]' => 'Testing',
            'ligne_devi[quantite]' => 'Testing',
            'ligne_devi[prixUnitaireHT]' => 'Testing',
            'ligne_devi[tauxTVA]' => 'Testing',
            'ligne_devi[montantHT]' => 'Testing',
            'ligne_devi[devis]' => 'Testing',
        ]);

        self::assertResponseRedirects('/ligne/devis');

        self::assertSame(1, $this->ligneDeviRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new LigneDevis();
        $fixture->setDesignation('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setPrixUnitaireHT('My Title');
        $fixture->setTauxTVA('My Title');
        $fixture->setMontantHT('My Title');
        $fixture->setDevis('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('LigneDevi');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new LigneDevis();
        $fixture->setDesignation('Value');
        $fixture->setQuantite('Value');
        $fixture->setPrixUnitaireHT('Value');
        $fixture->setTauxTVA('Value');
        $fixture->setMontantHT('Value');
        $fixture->setDevis('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ligne_devi[designation]' => 'Something New',
            'ligne_devi[quantite]' => 'Something New',
            'ligne_devi[prixUnitaireHT]' => 'Something New',
            'ligne_devi[tauxTVA]' => 'Something New',
            'ligne_devi[montantHT]' => 'Something New',
            'ligne_devi[devis]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ligne/devis');

        $fixture = $this->ligneDeviRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDesignation());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getPrixUnitaireHT());
        self::assertSame('Something New', $fixture[0]->getTauxTVA());
        self::assertSame('Something New', $fixture[0]->getMontantHT());
        self::assertSame('Something New', $fixture[0]->getDevis());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new LigneDevis();
        $fixture->setDesignation('Value');
        $fixture->setQuantite('Value');
        $fixture->setPrixUnitaireHT('Value');
        $fixture->setTauxTVA('Value');
        $fixture->setMontantHT('Value');
        $fixture->setDevis('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ligne/devis');
        self::assertSame(0, $this->ligneDeviRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
