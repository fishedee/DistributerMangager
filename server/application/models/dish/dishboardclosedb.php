<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishBoardCloseDb extends CI_Model{

    private $tableName = 't_dish_board_close';

    public function __construct(){
        parent::__construct();
    }

    //增加
    public function add($data){
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }
}
