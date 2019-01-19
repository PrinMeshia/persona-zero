<?php
declare (strict_types = 1);
namespace Controller\Main;

use Core\Module\Controller;
use Core\Http\Request;
use Model\Category;

class IndexController extends Controller{
    public function indexAction(Request $request){
        $text = $this->database->getManager(Category::class)->findAll();
        return $this->toJson($request, ["toto" => "test"]);
    }
}