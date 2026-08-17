<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Paiement;
use App\Form\PaiementType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

 #[Route('/paiment', name: 'paiement.')]
final class PaiementController extends AbstractController
{
    #[Route('/new/{factureId}', name: 'new', methods: ['POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        #[MapEntity(id: 'factureId')] Facture $facture
    ): Response {
        $paiement = new Paiement();
        $paiement->setFacture($facture);

        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($paiement);
            $em->flush();
        }

        return $this->redirectToRoute('facture.show', ['id' => $facture->getId()]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Paiement $paiement, EntityManagerInterface $em): Response
    {
        $factureId = $paiement->getFacture()->getId();

        if ($this->isCsrfTokenValid('delete'.$paiement->getId(), $request->request->get('_token'))) {
            $em->remove($paiement);
            $em->flush();
        }

        return $this->redirectToRoute('facture.show', ['id' => $factureId]);
    }
}
