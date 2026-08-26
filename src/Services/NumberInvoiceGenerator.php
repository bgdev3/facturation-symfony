<?php

namespace App\Services;

use App\Repository\FactureRepository;

/**
 * Service utile en prod afin de factoriser le calcul. 
 * Utile des qu'au moins 1 facture est crée.
 */
class NumberInvoiceGenerator
{
    public function __construct(private FactureRepository $repo) {}

    public function generate(): string
    {
        $year = (new \DateTimeImmutable())->format('Y');
        $lastFacture = $this->repo->findLastOfYear($year); // dernière facture de l'année, triée par numero DESC

        $nextNumber = $lastFacture 
            ? (int) substr($lastFacture->getNumero(), -3) + 1
            : 1;

        return sprintf('FAC-%s-%03d', $year, $nextNumber);
    }
}