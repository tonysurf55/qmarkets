<?php
namespace App\Models\Database;

use Closure;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use App\Models\Database\IDBService;

/**
 * Contains the DB logic for accessing connection functionality.
 */
class DBService implements IDBService, Result
{
    /** @var Connection  */
    private Connection $connection;
    
    /** @var Statement  */
    private Statement $statement;
    
    /**
     * Creates an instance of the class.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        protected EntityManagerInterface $entityManager,
    )
    {
        $this->connection = $entityManager->getConnection();
    }
    
    /**
     * Prepares the statement.
     *
     * @param string $sql
     *
     * @return DBService
     * @throws \Doctrine\DBAL\Exception
     */
    public function prepare(string $sql): self
    {
        $this->statement = $this->connection->prepare($sql);
        return $this;
    }
    
    /**
     * Adds value param
     */
    public function bindValue(string $name, $value, $type = ParameterType::STRING): self
    {
        $this->statement->bindValue($name, $value, $type);
        return $this;
    }
    
    /**
     * Adds value param
     */
    public function bindParam(string $name, $value, $type = ParameterType::STRING): self
    {
        $this->statement->bindParam($name, $value, $type);
        return $this;
    }
    
    /**
     * Returns the first value of the next row of the result or FALSE if there are no more rows.
     *
     * @return mixed|false
     *
     * @throws Exception
     */
    public function fetchOne()
    {
        $result = $this->executeQuery();
        return $result->fetchOne();
    }
    
    /**
     * Returns the next row of the result as a numeric array or FALSE if there are no more rows.
     *
     * @return array<int,mixed>|false
     *
     * @throws Exception
     */
    public function fetchNumeric()
    {
        $result = $this->executeQuery();
        return $result->fetchNumeric();
    }
    
    /**
     * Returns the next row of the result as an associative array or FALSE if there are no more rows.
     *
     * @return array<string,mixed>|false
     *
     * @throws Exception
     */
    public function fetchAssociative()
    {
        $result = $this->executeQuery();
        return $result->fetchAssociative();
    }
    
    /**
     * Returns an array containing all of the result rows represented as numeric arrays.
     *
     * @return array<int,array<int,mixed>>
     *
     * @throws Exception
     */
    public function fetchAllNumeric(): array
    {
        $result = $this->executeQuery();
        return $result->fetchAllNumeric();
    }
    
    /**
     * Returns an array containing all of the result rows represented as associative arrays.
     *
     * @return array<int,array<string,mixed>>
     *
     * @throws Exception
     */
    public function fetchAllAssociative(): array
    {
        $result = $this->executeQuery();
        return $result->fetchAllAssociative();
    }
    
    /**
     * Returns an array containing the values of the first column of the result.
     *
     * @return array<int,mixed>
     *
     * @throws Exception
     */
    public function fetchFirstColumn(): array
    {
        $result = $this->executeQuery();
        return $result->fetchFirstColumn();
    }
    
    /**
     * Returns the number of rows affected by the DELETE, INSERT, or UPDATE statement that produced the result.
     *
     * If the statement executed a SELECT query or a similar platform-specific SQL (e.g. DESCRIBE, SHOW, etc.),
     * some database drivers may return the number of rows returned by that query. However, this behaviour
     * is not guaranteed for all drivers and should not be relied on in portable applications.
     *
     * @return int The number of rows.
     */
    public function rowCount()
    {
        return $this->statement->rowCount();
    }
    
    /**
     * Returns the number of columns in the result
     *
     * @return int The number of columns in the result. If the columns cannot be counted,
     *             this method must return 0.
     */
    public function columnCount()
    {
        return $this->statement->columnCount();
    }
    
    /**
     * Discards the non-fetched portion of the result, enabling the originating statement to be executed again.
     */
    public function free(): void
    {
        $this->statement->free();
    }
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $statement
     *
     * @return int
     */
    public function executeStatement(array $params = []): int
    {
        return $this->statement->executeStatement($params);
    }
    
    /**
     * @param Statement $statement
     * @param array $params
     *
     * @return Result
     * @throws Exception
     */
    public function executeQuery(array $params = []): Result
    {
        return $this->statement->executeQuery($params);
    }
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $sql
     * @param array $params
     * @param array $types
     *
     * @return int
     */
    public function executeStatementSQL(string $sql, array $params, array $types = []): int
    {
        return $this->connection->executeStatement($sql, $params);
    }
    
    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     *
     * @return Result
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeSQL(string $sql, array $params = [], array $types = []): Result
    {
        return $this->connection->executeQuery($sql, $params, $types);
    }
    
    /**
     * @param Statement $statement
     * @param array $params
     *
     * @return Result
     * @throws Exception
     */
    public function executeQueryFromStatement(Statement $statement, array $params = []): Result
    {
        return $statement->executeQuery($params);
    }
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $statement
     *
     * @return int
     */
    public function executeStatementFromStatement(Statement $statement, array $params): int
    {
        return $statement->executeStatement($params);
    }
    
    /**
     * Gets the db connection object.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }
    
    /**
     * Executes a function in a transaction.
     *
     * The function gets passed this Connection instance as an (optional) parameter.
     *
     * If an exception occurs during execution of the function or transaction commit,
     * the transaction is rolled back and the exception re-thrown.
     *
     * @param Closure $func The function to execute transactionally.
     *
     * @return mixed The value returned by $func
     *
     * @throws \Throwable
     */
    public function transactional(Closure $func)
    {
        return $this->connection->transactional($func);
    }
}