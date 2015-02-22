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

    public function check($address){
        if(preg_match_all('/\w{1,}/',$address['name']) == 0 )
            throw new CI_MyException(1,'收货名字不能为空');
        if(preg_match_all('/\w{1,}/',$address['province']) == 0 )
            throw new CI_MyException(1,'收货省份不能为空');
        if(preg_match_all('/\w{1,}/',$address['city']) == 0 )
            throw new CI_MyException(1,'收货城市不能为空');
        if(preg_match_all('/\w{1,}/',$address['address']) == 0 )
            throw new CI_MyException(1,'收货地址不能为空');
        if(preg_match_all('/^\d{11}$/',$address['phone']) == 0 )
            throw new CI_MyException(1,'请输入11位数字的电话号码');
        if(preg_match_all('/^[12]$/',$address['payment']) == 0 )
            throw new CI_MyException(1,'请选择微信支付或货到付款');
    }

    public function get($clientId){

        $this->addOnce($clientId);

        return $this->addressDb->getByClientId($clientId)[0];
    }

    public function mod($clientId, $data){
        $this->addressDb->modByClientId($clientId, $data);
    }
}
