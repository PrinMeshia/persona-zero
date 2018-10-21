<?php
namespace Core\Render;


class Template  
{
    private $_data = [];
    private $_content = "";
    private $_router = "";
    public function __construct()
    {
    }
    public function __set(string $key, string $value):void
    {
        $this->_data[$key] = $value;
    }
    public function __get(string $key)
    {
        if (isset($this->_dataView[$key])) {
            return $this->_dataView[$key];
        } else if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return false;
        }
    }
    public function setContent(string $filepath)
    {
        $this->_content = $filepath;
    }
    public function render(string $tpl, array $params = [], array $global = []):string
    {
        ob_start();
        extract($params, EXTR_OVERWRITE);
        extract($global, EXTR_OVERWRITE);
        $content = file_get_contents($tpl);
        $content = str_replace("{body}", file_get_contents($this->_content), $content);
        $content = preg_replace_callback("#\{\s*file\s([^\}]+)\}#", function($matches) {
            return file_get_contents(dirname(PUBLIC_PATH)."/".$matches[1].".tpl");
        }, $content);
        $content = preg_replace("#\{\s*if([^\}]+)\}#", "<?php if($1):?>", $content);
        $content = preg_replace("#\{\s*else\s*\}#", "<?php else:?>", $content);
        $content = preg_replace("#\{\s*(\/if)\s*\}#", "<?php endif;?>", $content);
        $content = preg_replace("#\{\s*each([^\}]+)\}#", "<?php foreach($1):?>", $content);
        $content = preg_replace("#\{\s*(\/each)\s*\}#", "<?php endforeach;?>", $content);
        $content = preg_replace("#\{\s*path([^\}]+)\}#", '<?= $router->generateUri$1 ;?>' , $content);
        $content = str_replace('{$', '<?= $', $content);
        $content = str_replace('{=', '<?= ', $content);
        $content = str_replace(array("}", "{"), array("?>", "<?php "), $content);
        $tmpfile = dirname(PUBLIC_PATH) . '/stockage/tmp/' . basename($tpl) . '-' . md5(time()) . rand(0, 100) . '.php';
        file_put_contents($tmpfile, $content);
        require_once($tmpfile);
        $rendered = ob_get_contents();
        ob_end_clean();
        unlink($tmpfile);
        return $rendered;

        
    }


}