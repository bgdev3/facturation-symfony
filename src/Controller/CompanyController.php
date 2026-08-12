<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\CompanyType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CompanyController extends AbstractController
{
    #[Route('/company', name: 'company')]
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $company = $user->getCompany();

        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ( $form->isSubmitted() && $form->isValid())
            {
                $em->flush();
                $this->addFlash('success', 'Mise à jour réussi.');
                return $this->redirectToRoute('client.index');
            }

        return $this->render('company/edit.html.twig', [
            'form' => $form,
        ]);
    }
}
