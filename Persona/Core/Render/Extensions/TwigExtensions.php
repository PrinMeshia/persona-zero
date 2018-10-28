<?php

namespace Core\Render\Extensions;

use Psr\Container\ContainerInterface;
use Core\Http\Request;
use Core\Router\Router;

class TwigExtensions extends \Twig_Extension{
    private $router;
    private $request;
    private $root;
    public function __construct(ContainerInterface $container){
        $this->router = $container->get(Router::class);
        $this->request = $container->get(Request::class);
        $this->root = $container->get("rootfolder");
        $this->path = $container->get("path");
    }
    public function getFunctions(){
        return [
            new \Twig_SimpleFunction('path',[$this,'getPath']),
            new \Twig_SimpleFunction('isSubPath',[$this,'getSubPath']),
            new \Twig_SimpleFunction('field',[$this,'field'],['is_safe' => ['html'],'needs_context'=>true]),
            new \Twig_SimpleFunction('asset',[$this,'asset'])
        ];
    }

    /**
     * générate url from assets
     *
     * @param [type] $asset
     * @return void
     */
    public function asset($asset){
        return sprintf('%sassets/%s',$this->root.$this->path["public"], ltrim($asset, '/'));
    }
    /**
     * generate url from path
     *
     * @param string $path
     * @param array $params
     * @return string
     */
    public function getPath(string $path ,array $params = []):string{
        return $this->router->generateUri($path,$params) ?? "#";
    }
    public function getSubPath(string $path ):bool{
        $uri = $_SERVER["REQUEST_URI"] ?? "/";
        $expected = $this->router->generateUri($path);
        return strpos($uri,$expected) !== false;
    }
    /**
     * generate html input
     *
     * @param array $context
     * @param string $key
     * @param [type] $value
     * @param string $label
     * @param array $options
     * @return string
     */
    public function field(array $context,string $key,$value = null,string $label,array $options = []):string{
        $type = $options["type"] ?? 'text';
        $error = $this->getHtmlError($context,$key);
        $class = "form-group " . ($options["class"] ??'');
        $attributes = [
            "class" => "form-control"  ,
            "id" => $key,
            "name" => $key
        ];
        if($error){
            $class .=" has-danger";
            $attributes["class"] .= " form-control-danger";
        }
            
        switch ($type) {
            case 'textarea':
                $input = $this->textarea($attributes,$value);
                break;
            case 'select':
                $input =$this->select($attributes,$value,$options);
                break;
            case 'file':
                $input =$this->file($attributes);
                break;
            case 'hidden':
            default:
                $input = $this->input($attributes,$value,$type);
                break;
        }
        return "<div class=\"{$class}\">
                    <label for=\"content\" >{$label}</label>
                    {$input} 
                    {$error} 
                </div>";
    }

    private function getHtmlError(array $context, string $key){
        $error = $context["errors"][$key] ?? false;
        if($error){
            return  "<small class=\"form-text text-muted\">{$error}</small>";
        }
        return "";
        
    }
    private function textarea(array $attribute,$value):string{
        return "<textarea ".$this->getHtmlFromArray($attribute) ." rows=\"10\">{$value}</textarea>";
    }
    private function input(array $attribute,$value,string $type):string{
        return "<input type=\"{$type}\" ".$this->getHtmlFromArray($attribute) ."  value=\"{$value}\">";
    }
    private function file(array $attribute):string{
        return "<input type=\"file\" ".$this->getHtmlFromArray($attribute) ." >";
    }
    private function select(array $attribute,$value,array $options = []):string{
        if (array_key_exists('options', $options)) {
            $options = $options['options'];
            $htmlOption = array_reduce(array_keys($options),function(string $html, string $key) use($options,$value){
                $active = $options[$key] == $value ? "selected" : "";
                return $html . '<option value="'.$key.'" '.$active.'>'.$options[$key].'</option>';
            },"");
            return "<select ".$this->getHtmlFromArray($attribute) ." >{$htmlOption }</select>";
        }
        return '';
    }
    private function getHtmlFromArray(array $attribute) : string{
        return implode(' ',array_map(function($key,$value){
            return "$key=\"$value\"";
        },array_keys($attribute),$attribute));
    }
}