<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AddressAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/addressDb', 'addressDb');
        $this->load->model('user/userDb', 'userDb');
        $this->load->model('user/userPermission', 'userPermissionDb');
    }    

    public function getByUserId($userId){
        return $this->addressDb->getByUserId($userId);
    }

    public function search($dataWhere, $dataLimit){
        return $this->addressDb->search($dataWhere, $dataLimit);
    }

    public function get($addressId){
        return $this->addressDb->get($addressId); 
    }

    public function del($addressId){
        $this->db->where('addressId', $addressId);
        $this->db->delete($this->tableName);
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($addressId, $data){
        $this->db->where("addressId", $addressId);
        $this->db->update($this->tableName, $data);
    }

}
