<?php

namespace App\Tests\Controller;

use App\Entity\LigneFacture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LigneFactureControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;

    /** @var EntityRepository<LigneFacture> */
    private EntityRepository $ligneFactureRepository;
    private string $path = '/ligne/facture/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->ligneFactureRepository = $this->manager->getRepository(LigneFacture::class);

        foreach ($this->ligneFactureRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('LigneFacture index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first()->text());
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'ligne_facture[designation]' => 'Testing',
            'ligne_facture[quantite]' => 'Testing',
            'ligne_facture[prixUnitaireHT]' => 'Testing',
            'ligne_facture[tauxTVA]' => 'Testing',
            'ligne_facture[facture]' => 'Testing',
        ]);

        self::assertResponseRedirects('/ligne/facture');

        self::assertSame(1, $this->ligneFactureRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }

    public function testShow(): void
    {
        $fixture = new LigneFacture();
        $fixture->setDesignation('My Title');
        $fixture->setQuantite('My Title');
        $fixture->setPrixUnitaireHT('My Title');
        $fixture->setTauxTVA('My Title');
        $fixture->setFacture('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('LigneFacture');

        // Use assertions to check that the properties are properly displayed.
        $this->markTestIncomplete('This test was generated');
    }

    public function testEdit(): void
    {
        $fixture = new LigneFacture();
        $fixture->setDesignation('Value');
        $fixture->setQuantite('Value');
        $fixture->setPrixUnitaireHT('Value');
        $fixture->setTauxTVA('Value');
        $fixture->setFacture('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'ligne_facture[designation]' => 'Something New',
            'ligne_facture[quantite]' => 'Something New',
            'ligne_facture[prixUnitaireHT]' => 'Something New',
            'ligne_facture[tauxTVA]' => 'Something New',
            'ligne_facture[facture]' => 'Something New',
        ]);

        self::assertResponseRedirects('/ligne/facture');

        $fixture = $this->ligneFactureRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getDesignation());
        self::assertSame('Something New', $fixture[0]->getQuantite());
        self::assertSame('Something New', $fixture[0]->getPrixUnitaireHT());
        self::assertSame('Something New', $fixture[0]->getTauxTVA());
        self::assertSame('Something New', $fixture[0]->getFacture());

        $this->markTestIncomplete('This test was generated');
    }

    public function testRemove(): void
    {
        $fixture = new LigneFacture();
        $fixture->setDesignation('Value');
        $fixture->setQuantite('Value');
        $fixture->setPrixUnitaireHT('Value');
        $fixture->setTauxTVA('Value');
        $fixture->setFacture('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/ligne/facture');
        self::assertSame(0, $this->ligneFactureRepository->count([]));

        $this->markTestIncomplete('This test was generated');
    }
}
