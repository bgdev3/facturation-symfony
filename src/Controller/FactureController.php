<?php

namespace App\Controller;

use App\Entity\Facture;
use App\Entity\Paiement;
use App\Entity\User;
use App\Form\FactureType;
use App\Form\PaiementType;
use App\Repository\FactureRepository;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\UX\Turbo\TurboBundle;

#[Route('/facture', name: 'facture.')]
#[IsGranted('ROLE_USER')]
final class FactureController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, FactureRepository $repo): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $isAdmin = in_array('ROLE_ADMIN', $user->getRoles(), true);

        $factures = $repo->paginationInvoice( 
            $request->query->getInt('page', 1), 
            $isAdmin ? null : $user->getId() );

        return $this->render('facture/index.html.twig', [ 'factures' => $factures]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, NumberGenerator $number): Response
    {
        $facture = new Facture();
        $facture->setNumero($number->genererProchainNumeroFacture());

        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
            $facture->recalculerTotaux();
            $entityManager->persist($facture);
            $entityManager->flush();

            return $this->redirectToRoute('facture.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('facture/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted('POST_VIEW', 'facture')]
    public function show(Facture $facture): Response
    {
        $paiement = new Paiement();
        $paiement->setFacture($facture);

        $paiementForm = $this->createForm(PaiementType::class, $paiement);

        return $this->render('facture/show.html.twig', [
            'facture' => $facture,
            'paiementForm' => $paiementForm,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('POST_EDIT', 'facture')]
    public function edit(Request $request, Facture $facture, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FactureType::class, $facture);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if( $request->request->get('action') === 'save') {
                $facture->recalculerTotaux();
                 $em->flush();
                  return $this->redirectToRoute('facture.show', [ 'id' => $facture->getId() ], Response::HTTP_SEE_OTHER);
            }
             
            return $this->render('facture/edit_split.html.twig', [
                'form' => $form,
                'facture' => $facture,
            ]);
        }

        return $this->render('facture/edit_split.html.twig', ['form' => $form, 'facture' => $facture]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    #[IsGranted('POST_DELETE', 'facture')]
    public function delete(Request $request, Facture $facture, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$facture->getId(), $request->getPayload()->getString('_token'))) {
            
            $facture_id = $facture->getId();
            $entityManager->remove($facture);
            $entityManager->flush();

             if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('facture/delete_stream.html.twig', [
                    'id' => $facture_id,
        ]);
    }
        }

        return $this->redirectToRoute('facture.index', [], Response::HTTP_SEE_OTHER);
    }
}
