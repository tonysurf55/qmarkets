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
    public function searchUsers(string $pattern): array
    {

        $likeClause = '';
        $keywords = explode(' ', $pattern);
        foreach ($keywords as $key) {
            if ($likeClause) {
                $likeClause .= ' or ';
            }
            $likeClause .= "k.keyword like '{$key}%'";
        }

        $filterClause = '';

//        $likeClause = "k.keyword like '{$}%' or k.keyword like 'cob%'";



        $query = sprintf("
        	SELECT 
			u.uid, 
			u.name, 
			u.mail, 
			p.full_name,
			p.region,
			p.department,
			kk.score
		FROM users u
		left join profiles_view p on p.uid = u.uid
		inner join (
		select uid, score from keyword_scores ks 
			inner join keywords k on k.id = ks.keyword_id 
		and (%s) %s
-- 		and ks.region_id in (1, 2, 3, 4)
	) kk on kk.uid = u.uid
		order by kk.score desc
		limit 5", $likeClause, $filterClause);



//        $this->dbService->

        return $this->dbService->prepare($query)
//            ->bindParam('pattern', $searchPattern)
//            ->bindValue('patternWild', "%$searchPattern%")
//            ->bindValue('limit', 10)
            ->fetchAllAssociative();
    }

}