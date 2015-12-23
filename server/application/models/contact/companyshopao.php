<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CompanyShopAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('contact/companyShopDb','companyShopDb');
	}
	
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->companyShopDb->search($dataWhere,$dataLimit);
	}

	//获取门店
	public function get($userId,$companyShopId){
		$shop = $this->companyShopDb->get($companyShopId);
		if($shop){
			if($shop['userId'] != $userId){
				throw new CI_MyException(1,'非法查看');
			}else{
				return $shop;
			}
		}else{
			throw new CI_MyException(1,'无效门店id');
		}
	}

	//增加门店信息
	public function add($userId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'参数必填');
			}
		}
		$info = $this->getLocation($data['address']);
		$data['lat'] = $info['lat'];
		$data['lng'] = $info['lng'];
		$data['userId'] = $userId;
		return $this->companyShopDb->add($data);
	}

	//修改门店信息
	public function mod($userId,$companyShopId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'参数必填');
			}
		}
		$shop = $this->get($userId,$companyShopId);
		if($shop['address'] != $data['address']){
			$info = $this->getLocation($data['address']);
			$data['lat'] = $info['lat'];
			$data['lng'] = $info['lng'];
		}
		return $this->companyShopDb->mod($companyShopId,$data);
	}

	//删除门店信息
	public function del($userId,$companyShopId){
		$this->get($userId,$companyShopId);
		return $this->companyShopDb->del($companyShopId);
	}

	//前端获取门店信息
	public function getShop($userId){
		return $this->companyShopDb->getShop($userId);
	}

	private function getLocation($address){
		$ak  = 'GGs1ALDxkbobgtyXG60b9BRf';
		$url = 'http://api.map.baidu.com/geocoder/v2/?address='.$address.'&output=json&ak='.$ak;
		$addressInfo = $this->juhecurl($url);
		$addressInfo = json_decode($addressInfo,true);
		if($addressInfo['status'] == 0){
			return array(
				'lat'=>$addressInfo['result']['location']['lat'],
				'lng'=>$addressInfo['result']['location']['lng']
				);
		}else{
			throw new CI_MyException(1,'获取地址信息失败');
		}
	}

	private function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}
}
