<?php
namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Entity\Regions;

/**
 * Class RegionsRepository
 */
class RegionsRepository extends ServiceEntityRepository
{

    /**
     * Creates an instance of the class.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Regions::class);
    }
}