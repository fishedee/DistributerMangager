<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DishOrderBoardTeamDb extends CI_Model{

    private $tableName = 't_dish_order_board_team';

    public function __construct(){
        parent::__construct();
    }

    public function getTeam($boardId){
        $this->db->where('boardId',$boardId);
        $this->db->order_by('teamId','DESC');
        $result = $this->db->get($this->tableName)->first_row('array');
        if($result){
            return $result['team'];
        }else{
            return 1;
        }
    }

    public function addTeam($data){
        $this->db->insert($this->tableName,$data);
    }
}
