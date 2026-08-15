<?php

namespace App\Controller;

use App\Entity\LigneFacture;
use App\Form\LigneFactureType;
use App\Repository\LigneFactureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ligneFacture',name: 'ligneFacture.')]
final class LigneFactureController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(LigneFactureRepository $ligneFactureRepository): Response
    {
        return $this->render('ligne_facture/index.html.twig');
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ligneFacture = new LigneFacture();
        $form = $this->createForm(LigneFactureType::class, $ligneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ligneFacture);
            $entityManager->flush();

            return $this->redirectToRoute('ligneFacture.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_facture/new.html.twig', [
            'ligne_facture' => $ligneFacture,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(LigneFacture $ligneFacture): Response
    {
        return $this->render('ligne_facture/show.html.twig', [
            'ligneFacture' => $ligneFacture,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LigneFacture $ligneFacture, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LigneFactureType::class, $ligneFacture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ligneFacure.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ligne_facture/edit.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, LigneFacture $ligneFacture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ligneFacture->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ligneFacture);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ligneFacture.index', [], Response::HTTP_SEE_OTHER);
    }
}
