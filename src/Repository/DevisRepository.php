<?php

namespace App\Repository;

use App\Entity\Devis;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Devis>
 */
class DevisRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Devis::class);
    }

     public function paginationQuotation(?int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('d')
        ->join('d.client', 'c')
        ->orderBy('d.dateEmission', 'DESC');

        if ($userId !== null) {
            $builder->andWhere('c.user = :user')
                ->setParameter('user', $userId);
        }

        return $this->paginator->paginate(
            $builder,
            $page ?? 1,
            5,
            [
                'distinct' => true,
                'sortFieldAllowList' => ['d.id', 'd.numero', 'd.dateEmission'],
            ]
        );
    }

    //    /**
    //     * @return Devis[] Returns an array of Devis objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Devis
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
