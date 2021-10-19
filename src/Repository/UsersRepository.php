<?php
namespace App\Repository;

use App\Models\Database\IDBService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Entity\Users;

/**
 * Class UsersRepository
 */
class UsersRepository extends ServiceEntityRepository
{

    /**
     * Creates an instance of the class.
     *
     * @param ManagerRegistry $registry
     * @param IDBService $dbService
     */
    public function __construct(
        ManagerRegistry $registry,
        private IDBService $dbService,

    )
    {
        parent::__construct($registry, Users::class);
    }


    /**
     * Gets the values of the search pattern.
     *
     * @param string $searchPattern
     * @param int $limit
     *
     * @return array
     */
    public function searchUsers(): array
    {
        $query = '
            select * from users 
            limit 10
        ';
        return $this->dbService->prepare($query)
//            ->bindParam('pattern', $searchPattern)
//            ->bindValue('patternWild', "%$searchPattern%")
//            ->bindValue('limit', 10)
            ->fetchAllAssociative();
    }

}