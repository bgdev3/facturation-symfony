<?php

namespace App\Tests\Controller;

use App\Entity\Facture;
use App\Entity\Paiement;
use App\Enum\FactureStatut;
use App\Enum\PaiementStatus;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class PaiementControllerTest extends WebTestCase
{
    private EntityManagerInterface $em;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->em = static::getContainer()->get(EntityManagerInterface::class); 
    }

    private function createFacture(): Facture
    {
        $facture = new Facture();
        $facture->setNumero('FAC-2024-002');
        $facture->setDateEmission(new \DateTimeImmutable('2024-01-01'));
        $facture->setDateEcheance(new \DateTimeImmutable('2024-02-01'));
        $facture->setStatut(FactureStatut::Brouillon);
        $facture->setMontantHT('100.00');
        $facture->setMontantTVA('20.00');
        $facture->setMontantTTC('120.00');
        $facture->setConditionsPaiement('30 jours');

        $this->em->persist($facture);
        $this->em->flush();

        return $facture;
    }

    public function testNewPaiementRedirectsToFactureShow(): void
    {
       
        $facture = $this->createFacture();

        $facture = $this->em->getRepository(Facture::class)->findOneBy([]);

        if (!$facture) 
            $this->markTestSkipped('Aucune facture en base pour le test.');
        

        $crawler = $this->client->request('GET', '/facture/'.$facture->getId());
        $this->assertResponseIsSuccessful();

        $form = $crawler->filter('form[name="paiement"]')->form([
            'paiement[montant]' => '100.00',
            'paiement[date]' => (new \DateTime())->format('Y-m-d'),
            'paiement[moyen]' => 'virement',
            // 'paiement[facture]' => $facture->getId(),
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/facture/'.$facture->getId());
    }

public function testDeletePaiementRedirectsToFactureShow(): void
{
    $facture = $this->em->getRepository(Facture::class)->findOneBy([]);

    if (!$facture) {
        $this->markTestSkipped('Aucune facture en base pour le test.');
    }

    $paiement = new Paiement();
    $paiement->setFacture($facture);
    $paiement->setMontant('100.00');
    $paiement->setDate(new \DateTimeImmutable());
    $paiement->setMoyen(PaiementStatus::Virement);


    $this->em->persist($paiement);
    $this->em->flush();

    $factureId = $paiement->getFacture()->getId();

    $this->client->request('POST', '/paiement/'.$paiement->getId().'/delete');

    $this->assertResponseRedirects('/facture/'.$factureId);
}
}