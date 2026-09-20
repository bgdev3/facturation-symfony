<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Enum\DevisStatut;
use App\Event\DevisAccepteEvent;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
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

    #[Route('/new', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager, NumberGenerator $numberoGenerator ): Response
    {
        $devis = new Devis();
        $devis->setNumero($numberoGenerator->genererProchainNumeroDevis());

        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
         
            $entityManager->persist($devis);
            $devis->recalculerTotaux();
            $entityManager->flush();

            return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/create.html.twig', [ 'form' => $form ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Devis $devis): Response
    {
        return $this->render('devis/show.html.twig', [
            'devis' => $devis,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function editSplit(Request $request, Devis $devis, EntityManagerInterface $em, EventDispatcherInterface $dispatch): Response
    {
        $oldStatut = $devis->getStatut();
       
        $form = $this->createForm(DevisType::class, $devis);
        $form->handleRequest($request);
      
        if ($form->isSubmitted() && $form->isValid()) {

            if ($request->request->get('action') === 'save') {

                $devis->recalculerTotaux();
                $em->flush();

                if ($oldStatut !== $devis->getStatut() && $devis->getStatut() === DevisStatut::Accepte) {
                        $dispatch->dispatch(new DevisAccepteEvent($devis));
                    }
                return $this->redirectToRoute('devis.show', ['id' => $devis->getId() ], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->render('devis/edit_split.html.twig', ['devis' => $devis, 'form' => $form ]);
    }

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devis->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($devis);
            $entityManager->flush();
        }

        return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
    }
}
