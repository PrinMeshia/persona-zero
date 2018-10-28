<?php
namespace Helpers\Validators;
class ErrorValidator{
    private $key;
    private $rule;
    private $attributes;
    private $messages = [
        "required" => "Field %s is requred",
        "slug" => "Field %s has wrong slug format",
        "empty" => "Field %s cannot be empty",
        "betweenlength" => "Field %s must be between %d and %d characters",
        "minlength" => "Field %s must be less than %d characters",
        "maxlength" => "Field %s must be more than %d characters",
        "datetime" => "field %s must be in the format (%s)",
        "exists" => "Field %s does not exist in the table %s",
        "unique" => "Field %s muse be unique"   
    ];
    /**
     *
     * @param string $key
     * @param string $rule
     * @param array $attributes
     */
    public function __construct(string $key,string $rule,array $attributes = [])
    {
        $this->key = $key;
        $this->rule = $rule;
        $this->attributes = $attributes;
    }
    public function __toString()
    {
        $params = array_merge([$this->messages[$this->rule],$this->key],$this->attributes);
        return (string) \call_user_func_array("sprintf",$params);
    }
}