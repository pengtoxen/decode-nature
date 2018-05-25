<?php
namespace Home\Controller;
use Common\Common\Util;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        $home = Util::uriHost()."/public";
        header("Location: $home");
        exit;
    }
}