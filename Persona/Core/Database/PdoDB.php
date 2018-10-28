<?php
namespace Core\Database;
use Core\Database\Database;
use Core\Interfaces\PdoDBInterface;
use \PDO;
/**
 * Class PdoDB
 * @package Core\Database
 */
class PdoDB extends Database 
{
    /**
     * @var \PDO
     */
    private $connection;
    /**
     * @var array
     */
    private $managers = [];
    /**
     * @return \PDO
     */
    public function getconnection()
    {
        if ($this->connection === null) {
            $dsn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;
            try {
                $pdo = new PDO($dsn, $this->dbUser, $this->dbPass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
                $this->connection = $pdo;
            } catch (PDOException $e) {
                throw new \Exception('Error connecting to host. ' . $e->getMessage(), E_USER_ERROR);
            }
        }
        return $this->connection;
    }
    /**
     * get model manager
     *
     * @param string $model
     * @return void
     */
    public function getManager(string $model)
    {
        $managerClass = $model::getManager();
        $this->managers[$model] = $this->managers[$model] ?? new $managerClass($this->getconnection(), $model);
        return $this->managers[$model];
    }
}