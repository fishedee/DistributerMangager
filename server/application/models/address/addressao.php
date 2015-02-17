<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class AddressAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
        $this->load->model('address/addressDb', 'addressDb');
        $this->load->model('address/addressPaymentEnum', 'addressPaymentEnum');
    }    

    private function addOnce($clientId){
        $clients = $this->addressDb->getByClientId($clientId);
        if( count($clients) == 0 ){
            $this->addressDb->add(array(
                'clientId'=>$clientId,
                'payment'=>$this->addressPaymentEnum->WXPAY,
                'province'=>'广东',
                'city'=>'广州'
            ));
        }
    }

    public function get($clientId){

        $this->addOnce($clientId);

        return $this->addressDb->getByClientId($clientId)[0];
    }

    public function mod($clientId, $data){
        $this->addressDb->modByClientId($clientId, $data);
    }
}
