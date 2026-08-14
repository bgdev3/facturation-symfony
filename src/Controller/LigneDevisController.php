<?php

namespace App\Controller;

use App\Entity\LigneDevis;
use App\Form\LigneDevis1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ligneDevis', name: 'ligneDevis.')]
final class LigneDevisController extends AbstractController
{

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligneDevis = new LigneDevis();
        $form = $this->createForm(LigneDevis1Type::class, $ligneDevis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ligneDevis);
            $entityManager->flush();

            return $this->redirectToRoute('ligneDevis.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_devis/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneDevis $ligneDevis, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneDevis1Type::class, $ligneDevis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ligneDevis.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_devis/edit.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, LigneDevis $ligneDevis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneDevis->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ligneDevis);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ligneDevis.index', [], Response::HTTP_SEE_OTHER);
    }
}
