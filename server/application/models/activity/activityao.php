<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ActivityAo extends CI_Model {
	public function __construct(){
		parent::__construct();
		$this->load->model('activity/activityDb','activityDb');
	}

	//查看报名
	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		return $this->activityDb->search($dataWhere,$dataLimit);
	}

	//报名
	public function enList($userId,$clientId,$data){
		foreach ($data as $key => $value) {
			if(!$value){
				throw new CI_MyException(1,'报名填写信息均不能为空');
			}
		}
		if(isset($data['phone'])){
			if(preg_match_all('/^\d{11}$/',$data['phone']) == 0 )
            	throw new CI_MyException(1,'请输入11位数字的电话号码');
		}
        if($data['sex'] != '男' && $data['sex'] != '女'){
        	throw new CI_MyException(1,'性别只能填男或者女');
        }

        //检测报名资格
        // $result = $this->checkEnList($userId,$clientId);
        // if($result){
        // 	throw new CI_MyException(1,'您已经报名了,请勿重复报名');
        // }
        $data['userId'] = $userId;
        $data['clientId'] = $clientId;
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $result = $this->activityDb->enList($data);
        if($result){
        	return $result;
        }else{
        	throw new CI_MyException(1,'报名失败');
        }
	}

	//检测报名资格
	public function checkEnList($userId,$clientId){
		$result = $this->activityDb->checkEnList($userId,$clientId);
		return $result;
	}

	//获取报名信息
	public function enListed($userId){
		$result = $this->activityDb->enListed($userId);
		foreach ($result as $key => $value) {
			$result[$key]['phone'] = substr_replace($value['phone'], '*****', 4,5);
			$result[$key]['name']  = $this->cut_str($value['name'],1);
		}
		return $result;
	}

	private function cut_str($str,$len) {    
     	$j=strlen($str)/2-1;
	  	$m="";
	  	for($i=1;$i<=$j;$i++){
	  		$m=$m."*";
	  	}
  		$n = 0;    
     	$tempstr = '';    
    	for ($i=0; $i<$len; $i++) {    
        	if (ord(substr($str,$n,1)) > 224) {    
             	$tempstr .= substr($str,$n,3);    
            	$n += 3;    
             	$i++;    
        	} elseif (ord(substr($str,$n,1)) > 192) {    
             	$tempstr .= substr($str,$n,2);    
            	$n += 2;    
            	$i++;   
        	} else {    
            	$tempstr .= substr($str,$n,1);    
             	$n ++;    
        	}    
    	}
    	$m = substr($m, 1,strlen($m)-1);
	    return $tempstr.$m;
	} 
}
