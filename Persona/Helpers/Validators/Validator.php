<?php
namespace Helpers\Validators;
use Core\Database\Model;
use Core\Database\Manager;
class Validator
{
    private $params;
    private $errors = [];
    private $patterns = [
        "slug" => '/^([a-z0-9]+-?)+$/'
    ];
    public function __construct(array $params)
    {
        $this->params = $params;
    }
    /**
     * test if required Field exist
     *
     * @param string ...$keys
     * @return self
     */
    public function require(string ...$keys) : self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value)) {
                $this->addError($key, 'required');
            }
        }
        return $this;
    }
    /**
     * test slug format
     *
     * @param string $key
     * @return self
     */
    public function slug(string $key) : self
    {
        $slug = $this->getValue($key);
        if (!is_null($slug) && !preg_match($this->patterns['slug'], $slug)) {
            $this->addError($key, 'slug');
        }
        return $this;
    }
    /**
     * test if field is not empty
     *
     * @param string ...$keys
     * @return self
     */
    public function notEmpty(string ...$keys) : self
    {
        foreach ($keys as $key) {
            $value = $this->getValue($key);
            if (is_null($value) || empty($value) || trim($value) == "") {
                $this->addError($key, 'empty');
            }
        }
        return $this;
    }
    /**
     * Control value lenght
     *
     * @param string $key
     * @param integer $minLength
     * @param integer $maxLength
     * @return self
     */
    public function length(string $key, int $minLength = null, int $maxLength = null) : self
    {
        $value = $this->getValue($key);
        $valueLength = mb_strlen($value);
        if (!is_null($minLength) && !is_null($maxLength) && ($valueLength < $minLength || $valueLength > $maxLength)) {
            $this->addError($key, 'betweenlength', [$minLength, $maxLength]);
            return $this;
        }
        if (!is_null($minLength) && $valueLength < $minLength) {
            $this->addError($key, 'minlength', [$minLength]);
            return $this;
        }
        if (!is_null($maxLength) && $valueLength > $maxLength) {
            $this->addError($key, 'maxlength', [$maxLength]);
        }
        return $this;
    }
    /**
     * test if datetime is valid
     *
     * @param string $key
     * @param string $format
     * @return self
     */
    public function dateTime(string $key, string $format = 'Y-m-d H:i:s') : self
    {
        $value = $this->getValue($key);
        $datetime = \DateTime::createFromFormat($format, $value);
        $error = \dateTime::getLastErrors();
        if ($error['error_count'] > 0 || $error['warning_count'] > 0) {
            $this->addError($key, 'datetime', [$format]);
        }
        return $this;
    }
    public function exists(string $key, Manager $model) : self
    {
        $value = $this->getValue($key);
        $data = $model->findOneBy(["id" => $value]);
        if (!$data) {
            $this->addError($key, 'exists', [$model->getTable()]);
        }
        return $this;
    }
    public function unique(string $key, Manager $model,$exclude = null) : self
    {
        $value = $this->getValue($key);
        $sql = 'SELECT * FROM ' . $model->getTable() . ' WHERE ' . $key . ' = ?';
        $params = [$value]; 
        if(!is_null($exclude)){
            $sql .= " AND id <> ?";
            $params[] = $exclude;
        }
        if ($model->query($sql,$params)) {
            $this->addError($key, 'unique', [$value]);
        }
        return $this;
    }
    /**
     * return all errors in array
     * 
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
    public function isValid() : bool
    {
        return empty($this->errors);
    }
    /**
     * save error
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     * @return void
     */
    private function addError(string $key, string $rule, array $attributes = []) : void
    {
        $this->errors[$key] = new ErrorValidator($key, $rule, $attributes);
    }
    /**
     * get value from key
     *
     * @param string $key
     * @return void
     */
    private function getValue(string $key)
    {
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return null;
    }
}