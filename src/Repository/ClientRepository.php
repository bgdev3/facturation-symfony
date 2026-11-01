<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private readonly PaginatorInterface $paginator)
    {
        parent::__construct($registry, Client::class);
    }

    /**
     * @return PaginationInterface<int, client>
     */
    public function paginationClient(?int $page): PaginationInterface
    {
        $builder =  $this->createQueryBuilder('p')->select('p');

        return $this->paginator->paginate(
            $builder,
            $page,
            5, [
                    'distinct' => true,
                    'sortFieldAllowList' => ['r.id', 'r.title'],
                ]
        );
    }

    /**
     * @return Client[]
     */
    public function searchByName(string $nom, Company $company): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.company = :company')
            ->andWhere('LOWER(c.nom) LIKE :nom')
            ->setParameter('company', $company)
            ->setParameter('nom', '%' . mb_strtolower($nom) . '%')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();
    }
    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
