<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client', name: 'client.')]
final class ClientController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ClientRepository $repo): Response
    {
        // $clients = $repo->findBy(['user' => $this->getUser()]);
        $page = $request->query->getInt('page', 1);
        $clients = $repo->paginationClient($page);
        return $this->render('client/index.html.twig', ['clients' => $clients]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $client = new Client();
        $client->setUser($this->getUser());

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client bien enregistré.');
           return $this->redirectToRoute('client.index');
        }
        return $this->render('client/create.html.twig', ['form' => $form]);
    }

    #[Route('/edit/{id}', name: 'edit', requirements:['id' => Requirement::DIGITS])]
    #[IsGranted('EDIT',  subject: 'client')]
    public function edit(Client $client, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($client);
            $em->flush();

            $this->addFlash('success', 'Client a bien été mis à jour.');
           return $this->redirectToRoute('client.index');
        }
        return $this->render('client/edit.html.twig', ['form' => $form]);
    }

    #[Route('/show/{id}', name: 'show', requirements:['id' => Requirement::DIGITS])]
    public function show(Client $client): Response
    {
        return $this->render('client/show.html.twig', ['client' => $client]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements:['id' => Requirement::DIGITS])]
    #[IsGranted('DELETE',  subject: 'client')]
    public function delete(Request $request, Client $client, EntityManagerInterface $em): Response
    {
         if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->getPayload()->getString('_token'))) {
            $em->remove($client);
            $em->flush();
         }

            $this->addFlash('success', 'Client supprimée.');
           return $this->redirectToRoute('client.index');
    }
}
