<?php
namespace App\Models\Database;

use Closure;
use Doctrine\DBAL\Statement;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Doctrine\DBAL\Driver\Result;
use Doctrine\DBAL\Driver\Exception;

/**
 * Contains the interface logic for accessing connection functionality.
 */
interface IDBService
{
    /**
     * Prepares the statement.
     *
     * @param string $sql
     *
     * @return DBService
     * @throws \Doctrine\DBAL\Exception
     */
    public function prepare(string $sql): self;
    
    /**
     * Adds value param
     */
    public function bindValue(string $name, $value, $type = ParameterType::STRING): self;
    
    /**
     * Adds value param
     */
    public function bindParam(string $name, $value, $type = ParameterType::STRING): self;
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $statement
     *
     * @return int
     */
    public function executeStatement(array $params = []): int;
    
    /**
     * @param Statement $statement
     * @param array $params
     *
     * @return Result
     * @throws Exception
     */
    public function executeQuery(array $params = []): Result;
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $sql
     * @param array $params
     * @param array $types
     *
     * @return int
     */
    public function executeStatementSQL(string $sql, array $params, array $types = []): int;
    
    /**
     * @param string $sql
     * @param array $params
     * @param array $types
     *
     * @return Result
     * @throws \Doctrine\DBAL\Exception
     */
    public function executeSQL(string $sql, array $params = [], array $types = []): Result;
    
    /**
     * @param Statement $statement
     * @param array $params
     *
     * @return Result
     * @throws Exception
     */
    public function executeQueryFromStatement(Statement $statement, array $params = []): Result;
    
    /**
     * Executes a statement and returns the effected row IDs.
     *
     * @param string $statement
     *
     * @return int
     */
    public function executeStatementFromStatement(Statement $statement, array $params): int;
    
    /**
     * Gets the db connection object.
     *
     * @return Connection
     */
    public function getConnection(): Connection;
    
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
    public function transactional(Closure $func);
}