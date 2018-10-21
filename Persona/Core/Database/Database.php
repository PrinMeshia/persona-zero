<?php 
namespace Core\Database;


/**
 * 
 */
class Database
{
    /**
     * @param $dbName
     * @param string $dbUser
     * @param string $dbPass
     * @param string $dbHost
     */
    public function __construct(array $data)
    {
        if ($data) {
            $this->dbName = $data['dbname'];
            $this->dbHost =$data['host'];
            $this->dbUser =$data['user'];
            $this->dbPass =$data['password']; 
        }
    }

}
