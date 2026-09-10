<?php
namespace App\Services;

use App\Entity\Devis;
use App\Entity\Facture;
use Doctrine\ORM\EntityManagerInterface;

class NumberGenerator
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function genererProchainNumeroDevis(): string
    {
        return $this->genererProchainNumero(Devis::class, 'DEV');
    }

    public function genererProchainNumeroFacture(): string
    {
        return $this->genererProchainNumero(Facture::class, 'FAC');
    }

    private function genererProchainNumero(string $entityClass, string $prefixe): string
    {
        $year = date('Y');

        $dernier = $this->em->getRepository($entityClass)
            ->createQueryBuilder('e')
            ->where('e.numero LIKE :pattern')
            ->setParameter('pattern', "{$prefixe}-{$year}-%")
            ->orderBy('e.numero', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        $compteur = 1;
        if ($dernier) {
            $compteur = (int) substr($dernier->getNumero(), -3) + 1;
        }

        return sprintf('%s-%s-%03d', $prefixe, $year, $compteur);
    }
}