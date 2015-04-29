<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
* 默认跳转到/backstage/login.html
*/

class Index extends CI_Controller{
    public function index(){
    header("Location:/backstage/login.html");
    }

}

?>
