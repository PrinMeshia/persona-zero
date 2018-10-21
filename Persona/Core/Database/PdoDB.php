<?php
namespace Core\Database;
use Core\Database\Database;
use Core\Interfaces\PdoDBInterface;
/**
 * Class PdoDB
 * @package Core\Database
 */
class PdoDB extends Database 
{
    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var array
     */
    private $managers = [];
    /**
     * @return \PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }
    public function getManager($model)
    {
        $managerClass = $model::getManager();
        $this->managers[$model] = $this->managers[$model] ?? new $managerClass($this->pdo, $model);
        return $this->managers[$model];
    }
}