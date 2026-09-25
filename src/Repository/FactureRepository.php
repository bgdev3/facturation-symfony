<?php

namespace App\Repository;

use App\Entity\Facture;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Facture>
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Facture::class);
    }

    public function paginationInvoice(?int $page, ?int $userId): PaginationInterface
    {
        $builder = $this->createQueryBuilder('f')
        ->join('f.client', 'c')
        ->orderBy('f.dateEmission', 'DESC');

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
                'sortFieldAllowList' => ['f.id', 'f.numero', 'f.dateEmission'],
            ]
        );
    }

    public function findLastOfYear(string $year): ?Facture
    {
        return  $this->createQueryBuilder('f')
                ->andWhere('f.numero LIKE :prefix')
                ->setParameter(':prefix', sprintf('FAC-%s-%%', $year))
                ->orderBy('f.dateEmission', 'DESC')
                ->addOrderBy('f.id', 'DESC')
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
    }
    
    public function findByUser(User $user): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.client', 'c')
            ->where('c.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Facture[] Returns an array of Facture objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Facture
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
