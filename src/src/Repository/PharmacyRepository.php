<?php

namespace App\Repository;

use App\Entity\Pharmacy;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;

/**
 * @extends ServiceEntityRepository<Pharmacy>
 *
 * @method Pharmacy|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pharmacy|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pharmacy[]    findAll()
 * @method Pharmacy[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PharmacyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pharmacy::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Pharmacy $entity, bool $flush = false): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Pharmacy $entity, bool $flush = false): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

//    /**
//     * @return Pharmacy[] Returns an array of Pharmacy objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Pharmacy
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
    public function findByParamsFromRequest(Request $request)
    {
        $fields = $request->query->all();
        $possibleColumns = $this->_em->getClassMetadata('App\\Entity\\Pharmacy')->getColumnNames();
        $queryBuilder = $this->createQueryBuilder('p');
        if(count($fields) > 0)
        {
            $i = 0;
            foreach($fields as $fieldName => $fieldValue)
            {
                if(!in_array($fieldName, $possibleColumns))
                {
                    continue;
                }
                $queryBuilder->andWhere("p.{$fieldName} LIKE :val{$i}")->setParameter("val{$i}", '%'.$fieldValue.'%');
                $i++;
            }
        }
        return $queryBuilder->getQuery()->getResult();
    }
}
