<?php

namespace App\Repository;

use App\Entity\Course;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Course|null find($id, $lockMode = null, $lockVersion = null)
 * @method Course|null findOneBy(array $criteria, array $orderBy = null)
 * @method Course[]    findAll()
 * @method Course[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    /**
     * @param string $entityName is the name of the entity joined with course entity
     * @return Course[] Returns an array of Course objects
     */
    public function findAllCoursesFromSpecificUser($entityName, $value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere("c.$entityName = :val")
            ->setParameter('val', $value)
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findOneByPublishedDateAndSlug(int $year, int $month, int $day, string $slug): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('YEAR(c.createdAt) = :year')
            ->andWhere('MONTH(c.createdAt) = :month')
            ->andWhere('DAY(c.createdAt) = :day')
            ->andWhere('c.slug = :slug')
            ->setParameters([
                'slug' => $slug,
                'year' => $year,
                'month' => $month,
                'day' => $day,
            ])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    // /**
    //  * @return Course[] Returns an array of Course objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Course
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}