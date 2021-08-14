<?php

namespace App\Repository;

use App\Entity\Trick;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Trick|null find($id, $lockMode = null, $lockVersion = null)
 * @method Trick|null findOneBy(array $criteria, array $orderBy = null)
 * @method Trick[]    findAll()
 * @method Trick[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrickRepository extends ServiceEntityRepository
{
    public const ALIAS = 't';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Trick::class);
    }


    public function findAllForHome()
    {
        return $this->createQueryBuilder(self::ALIAS)
            ->select(
                self::ALIAS,
                PictureRepository::ALIAS
            )

            ->leftJoin(self::ALIAS . '.mainPicture', PictureRepository::ALIAS)
            ->orderBy(self::ALIAS . '.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findCountForName(string $name, $id = null)
    {
        $builder = $this->createQueryBuilder(self::ALIAS)
            ->select("COUNT('name')");

        if ($id === null) {
            $builder->where(self::ALIAS . '.name = :name')
                ->setParameter('name', $name);
        } else {
            $builder->where(self::ALIAS . '.name = :name')
                ->andWhere(self::ALIAS . '.id != :id')
                ->setParameters(['name' => $name, 'id' => $id]);
        }
        return $builder->getQuery()->getSingleScalarResult();
    }
    // /**
    //  * @return Trick[] Returns an array of Trick objects
    //  */
    /*

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Trick
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
