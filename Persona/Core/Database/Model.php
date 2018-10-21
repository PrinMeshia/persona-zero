<?php
namespace Core\Database;

use Core\Exceptions\DBException;

/**
 * Class Model
 * @package Core\Database
 */
abstract class Model
{
    /**
     * @var array
     */
    public $originalData = [];
    /**
     * @return array
     */
    public abstract static function metadata();
    /**
     * @return string
     */
    public abstract static function getManager();
    /**
     * @param array $result
     * @return Model
     * @throws DBException
     */
    public function hydrate($result)
    {
        if(empty($result)) {
            throw new DBException("Aucun résultat n'a été trouvé !");
        }
        $this->originalData = $result;
        foreach($result as $column => $value) {
            $this->hydrateProperty($column, $value);
        }
        return $this;
    }
    /**
     * @param string $column
     * @param mixed $value
     */
    private function hydrateProperty($column, $value)
    {
        switch($this::metadata()["columns"][$column]["type"]) {
            case "integer":
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}((int) $value);
                break;
            case "string":
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}($value);
                break;
            case "datetime":
                $datetime = \DateTime::createFromFDBat("Y-m-d H:i:s", $value);
                $this->{sprintf("set%s", ucfirst($this::metadata()["columns"][$column]["property"]))}($datetime);
                break;
        }
    }
    /**
     * @param string $column
     * @return mixed
     */
    public function getSQLValueByColumn($column)
    {
        $value = $this->{sprintf("get%s", ucfirst($this::metadata()["columns"][$column]["property"]))}();
        if($value instanceof \DateTime){
            return $value->fDBat("Y-m-d H:i:s");
        }
        return $value;
    }
    /**
     * @param mixed $value
     */
    public function setPrimaryKey($value)
    {
        $this->hydrateProperty($this::metadata()["primaryKey"], $value);
    }
    /**
     * @return mixed
     */
    public function getPrimaryKey()
    {
        $primaryKeyColumn = $this::metadata()["primaryKey"];
        $property = $this::metadata()["columns"][$primaryKeyColumn]["property"];
        return $this->{sprintf("get%s", ucfirst($property))}();
    }
}