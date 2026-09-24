<?php

namespace App\Controller;

use App\Entity\Devis;
use App\Enum\DevisStatut;
use App\Event\DevisAccepteEvent;
use App\Event\DevisSendEvent;
use App\Form\DevisType;
use App\Repository\DevisRepository;
use App\Services\NumberGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\UX\Turbo\TurboBundle;

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

                if ($oldStatut !== $devis->getStatut()) { 

                    $event = match ($devis->getStatut()) {
                        DevisStatut::Accepte => new DevisAccepteEvent($devis),
                        DevisStatut::Envoye => new DevisSendEvent($devis),
                        default => null
                    };
                   if ($event !== null) {
                        $dispatch->dispatch($event);
                    }
                    return $this->redirectToRoute('devis.show', ['id' => $devis->getId() ], Response::HTTP_SEE_OTHER);
                }
            }
        }
        return $this->render('devis/edit_split.html.twig', ['devis' => $devis, 'form' => $form ]);
    }
    

    #[Route('/{id}', name: 'delete', methods: ['POST'])]
    public function delete(Request $request, Devis $devis, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devis->getId(), $request->getPayload()->getString('_token'))) {

            $devisId = $devis->getId();
            $entityManager->remove($devis);
            $entityManager->flush();

               if (TurboBundle::STREAM_FORMAT === $request->getPreferredFormat()) {
                $request->setRequestFormat(TurboBundle::STREAM_FORMAT);
                return $this->render('devis/delete_stream.html.twig', [
                    'id' => $devisId
                ]);
            }
        }
        return $this->redirectToRoute('devis.index', [], Response::HTTP_SEE_OTHER);
    }
}
