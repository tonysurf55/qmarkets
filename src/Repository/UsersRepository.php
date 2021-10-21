<?php
namespace App\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

use App\Entity\Users;
use App\Models\Database\IDBService;

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
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function runIndex()
    {
        $this->dbService->executeSQL('call generate_keywords()');
    }

    /**
     * Gets the values of the search pattern.
     *
     * @param string $pattern
     * @param array|null $filter_region
     * @param array|null $filter_department
     * @return array
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function searchUsers(string $pattern, ?array $filter_region = [], ?array $filter_department = []): array
    {
        [ $search, $regions, $departments, $params, $types ]
            = $this->createSearchParams($pattern, $filter_region, $filter_department);

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
                select uid, sum(score) as score from keyword_scores ks 
                    inner join keywords k on k.id = ks.keyword_id 
                    and (%s) %s %s
                    group by uid
            ) kk on kk.uid = u.uid
                order by kk.score desc
                limit 5"
            , $search, $regions, $departments);

        return $this->dbService
            ->executeSQL($query, $params, $types)
            ->fetchAllAssociative();
    }

    /**
     * @param string $pattern
     * @param array|null $filter_region
     * @param array|null $filter_department
     * @return array
     */
    private function createSearchParams(string $pattern, ?array $filter_region = [], ?array $filter_department = []): array
    {
        $search = $regions = $departments = '';
        $params = $types = [];
        $keywords = explode(' ', $pattern);

        // Search pattern
        foreach ($keywords as $key) {
            $key = trim($key);
            if (!$key) {
                continue;
            }

            $search .= $search ? ' or ' : '';
            $search .= "k.keyword like ?";
            $params[] = "{$key}%";
            $types[] = ParameterType::STRING;
        }

        // Region filter
        if ($filter_region) {
            $params[] = $filter_region;
            $types[] = Connection::PARAM_STR_ARRAY;
            $regions = sprintf("and %s in (?)", 'ks.region_id');
        }

        // Department filter
        if ($filter_department) {
            $params[] = $filter_department;
            $types[] = Connection::PARAM_STR_ARRAY;
            $departments = sprintf("and %s in (?)", 'ks.department_id');
        }

        return [ $search, $regions, $departments, $params, $types ];
    }
}