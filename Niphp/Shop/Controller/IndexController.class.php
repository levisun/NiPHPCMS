<?php
namespace Shop\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        // $this->show('shop','utf-8');
        // $data = I('get.');
        $data = LANG_SET . MODULE_NAME . CONTROLLER_NAME . ACTION_NAME . implode('', $_GET);
        print_r($_GET);
        var_dump($data);
    }
}