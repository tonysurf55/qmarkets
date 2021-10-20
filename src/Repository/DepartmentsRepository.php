<?php
namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Entity\Departments;

/**
 * Class DepartmentsRepository
 */
class DepartmentsRepository extends ServiceEntityRepository
{

    /**
     * Creates an instance of the class.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Departments::class);
    }
}