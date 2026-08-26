<?php

namespace App\Tests\Controller;

use App\Entity\Facture;
use App\Enum\FactureStatut;
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
    }

    public function testNew(): void
    {
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        // TODO: confirmer le texte exact du bouton submit (Twig)
        $this->client->submitForm('Save', [
            'facture[numero]' => 'FAC-2024-001',
            'facture[dateEmission]' => '2024-01-15',
            'facture[dateEcheance]' => '2024-02-15',
            'facture[statut]' => FactureStatut::Brouillon->value,
            'facture[montantHT]' => '100.00',
            'facture[montantTVA]' => '20.00',
            'facture[montantTTC]' => '120.00',
            'facture[conditionsPaiement]' => '30 jours',
        ]);

        self::assertResponseRedirects('/facture/');

        self::assertSame(1, $this->factureRepository->count([]));
    }

    public function testShow(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('FAC-2024-002');
        $fixture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $fixture->setDateEcheance(new \DateTimeImmutable('2024-02-01'));
        $fixture->setStatut(FactureStatut::Brouillon);
        $fixture->setMontantHT('100.00');
        $fixture->setMontantTVA('20.00');
        $fixture->setMontantTTC('120.00');
        $fixture->setConditionsPaiement('30 jours');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Facture');
    }

    public function testEdit(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('FAC-2024-003');
        $fixture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $fixture->setDateEcheance(new \DateTimeImmutable('2024-02-01'));
        $fixture->setStatut(FactureStatut::Brouillon);
        $fixture->setMontantHT('100.00');
        $fixture->setMontantTVA('20.00');
        $fixture->setMontantTTC('120.00');
        $fixture->setConditionsPaiement('30 jours');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        // TODO: confirmer le texte exact du bouton submit (Twig)
        $this->client->submitForm('Update', [
            'facture[numero]' => 'FAC-2024-003',
            'facture[dateEmission]' => '2024-03-01',
            'facture[dateEcheance]' => '2024-04-01',
            'facture[statut]' => 'envoyée', // adapter au vrai cas de l'enum
            'facture[montantHT]' => '200.00',
            'facture[montantTVA]' => '40.00',
            'facture[montantTTC]' => '240.00',
            'facture[conditionsPaiement]' => '60 jours',
        ]);

        self::assertResponseRedirects('/facture/');

        $fixture = $this->factureRepository->findAll();

        self::assertSame('FAC-2024-003', $fixture[0]->getNumero());
        self::assertSame('2024-03-01', $fixture[0]->getDateEmission()->format('Y-m-d'));
        self::assertSame('2024-04-01', $fixture[0]->getDateEcheance()->format('Y-m-d'));
        self::assertSame('envoyée', $fixture[0]->getStatut()->value);
        self::assertSame('200.00', $fixture[0]->getMontantHT());
        self::assertSame('40.00', $fixture[0]->getMontantTVA());
        self::assertSame('240.00', $fixture[0]->getMontantTTC());
        self::assertSame('60 jours', $fixture[0]->getConditionsPaiement());
    }

    public function testRemove(): void
    {
        $fixture = new Facture();
        $fixture->setNumero('FAC-2024-004');
        $fixture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $fixture->setDateEcheance(new \DateTimeImmutable('2024-02-01'));
        $fixture->setStatut(FactureStatut::Brouillon);
        $fixture->setMontantHT('100.00');
        $fixture->setMontantTVA('20.00');
        $fixture->setMontantTTC('120.00');
        $fixture->setConditionsPaiement('30 jours');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        // TODO: confirmer le texte exact du bouton submit (Twig)
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/facture/');
        self::assertSame(0, $this->factureRepository->count([]));
    }
}