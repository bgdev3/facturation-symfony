<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/devis', name:'devis.')]
final class DevisController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(DevisRepository $repo): Response
    {
        $devis = $repo->findAll();
        return $this->render('devis/index.html.twig', ['devis' => $devis]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devis = new Devis();
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devis);
            $entityManager->flush();

            return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/create.html.twig', [ 'form' => $form ]);
    }

    #[Route('show/{id}', name: 'show', methods: ['GET'])]
    public function show(Devis $devis): Response
    {
        return $this->render('devis/show.html.twig', [
            'devi' => $devis,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/edit.html.twig', ['form' => $form ]);
    }

    #[Route('/{id}', name: 'devis.delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devis->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($devis);
            $entityManager->flush();
        }

        return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
    }
}
