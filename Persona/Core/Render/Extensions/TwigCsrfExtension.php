<?php

namespace Core\Render\Extensions;

use Core\Middleware\CsrfMiddleware;


class TwigCsrfExtension extends \Twig_Extension
{
    private $CsrfMiddleware;
    public function __construct(CsrfMiddleware $CsrfMiddleware)
    {
        $this->CsrfMiddleware = $CsrfMiddleware;
    }
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('csrf_input',[$this,'csrfInput'],['is_safe' => ['html']])
        ];
    }
    public function csrfInput(){
        return '<input type="hidden" '. 
            'name="'.$this->CsrfMiddleware->getFormKey().'" '. 
            'value="'.$this->CsrfMiddleware->generateToken().'"/>';
    }
}
