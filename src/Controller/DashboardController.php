<?php

namespace App\Controller;

use App\Repository\DevisRepository;
use App\Repository\FactureRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(FactureRepository $factureRepo, DevisRepository $devisRepo): Response
    {
        $facturesPayees = $factureRepo->findBy(['statut' => 'payee']);
        $facturesImpayees = $factureRepo->findBy(['statut' => 'impayee']);
        $facturesEnRetard = $factureRepo->findFacturesEnRetard(); // méthode custom : dateEcheance < now && statut != payee

        return $this->render('dashboard/index.html.twig', [
            'caduMois' => array_sum(array_map(fn($f) => $f->getMontantTTC(), $facturesPayees)),
            'nbFacturesImpayees' => count($facturesImpayees),
            'montantImpaye' => array_sum(array_map(fn($f) => $f->getMontantTTC(), $facturesImpayees)),
            'nbDevisEnAttente' => count($devisRepo->findBy(['statut' => 'envoye'])),
            'nbFacturesEnRetard' => count($facturesEnRetard),
            'facturesRecentes' => $factureRepo->findBy([], ['dateEmission' => 'DESC'], 5),
            'devisRecents' => $devisRepo->findBy([], ['dateEmission' => 'DESC'], 5),
        ]);
    }
}
