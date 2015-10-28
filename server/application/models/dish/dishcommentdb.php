<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishCommentDb extends CI_Model{

	private $tableName = 't_dish_comment';

    public function __construct(){
        parent::__construct();
    }

    public function publish($data){
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //查看评论
    public function search($dataWhere,$limit){
    	if( isset($limit["pageIndex"]) && isset($limit["pageSize"]) ){
            $this->db->limit($limit["pageSize"], $limit["pageIndex"]);
        }
        foreach ($dataWhere as $key => $value) {
            if($key == 'dishId'){
                $this->db->where($key,$value);
            }else{
                $this->db->like($key,$value,'both');
            }
        }
        $this->db->order_by('commentId','asc');
        $query = $this->db->get($this->tableName)->result_array();
        return array(
        	'count'=>count($query),
        	'data' =>$query
        	);
    }
}
