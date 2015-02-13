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
        $this->addressDb->del($addressId);
    }

    public function add($data){
        return $this->addressDb->add($data);
    }

    public function mod($addressId, $data){
        $this->addressDb->mod($addressId, $data);
    }

    public function modByUserId($userId, $data){
        $address = $this->addressDb->getByUserId($userId);
        $addressId = $address['addressId'];
        $this->addressDb->mod($addressId, $data);
    }
}
