<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
	    $this->load->model('distribution/distributionDb', 'distributionDb');
        $this->load->model('distribution/distributionstateEnum', 'distributionStateEnum');
        $this->load->model('user/userAo','userAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    private function getOtherInfo($distribution){
        $distribution['upUserCompany'] = $this->userAo->get($distribution['upUserId'])['company'];
        $distribution['downUserCompany'] = $this->userAo->get($distribution['downUserId'])['company'];
        $distribution['distributionPercentShow'] = $this->getFixedPrice($distribution['distributionPercent']/100).'%';
        return $distribution;
    }

    public function search($where, $limit){
        $data = $this->distributionDb->search($where, $limit);

        foreach($data['data'] as $key=>$value){
            $data['data'][$key] = $this->getOtherInfo($data['data'][$key]);
        }
        return $data;
    }

    private function check($upUserId, $downUserId){
        return 0;
    }

    public function get($userId, $distributionId){
        $distribution = $this->distributionDb->get($distributionId);
        if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
            throw new CI_MyException(1, "无权查询此非本用户的分成关系");
         
        $distribution = $this->getOtherInfo($distribution);
        return $distribution;
    }

    public function mod($distributionId, $data){
        $this->distributionDb->mod($distributionId, $data);
    }

    public function del($userId, $distributionId){
        $distribution = $this->get($userId, $distributionId);
        $this->distributionDb->del($distribution['distributionId']);
    }

    public function request($upUserId, $downUserId){
        $this->check($upUserId, $downUserId);
        $this->distributionDb->add(array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
            'state'=>$this->distributionStateEnum->ON_REQUEST
        )); 
    }

    public function accept($userId,$distributionId){
        $distribution = $this->get($userId, $distributionId);
        if($distribution['upUserId'] != $userId)
            throw new CI_MyException(1, "无权同意本用户的分成关系");
        $this->distributionDb->mod($distributionId,array(
            'state'=>$this->distributionStateEnum->ON_ACCEPT
        ));
    }

    public function modPrecent($userId,$distributionId,$distributionPercentShow){
        $distribution = $this->get($userId, $distributionId);
        if($distribution['upUserId'] != $userId)
            throw new CI_MyException(1, "无权同意本用户的分成关系");

        $distributionPercent = intval(floatval($distributionPercentShow)*100);
        if($distributionPercent >= 10000)
            throw new CI_MyException(1, "默认分成比例不能大于100%");
        if($distributionPercent < 0)
            throw new CI_MyException(1, "默认分成比例不能少于0");

        $this->distributionDb->mod($distributionId,array(
            'distributionPercent'=>$distributionPercent
        ));
    }

    private $path = array();
    private $result = array();

    
    private function dfs($originUserId, $userId){
<<<<<<< HEAD
        //记录路径
        $this->path[$originUserId] = 1;
        $this->result_path[] = $originUserId;
    	if($originUserId == $userId){
    		return true;
    	}

        //遍历下级分类
    	$where = array(
    		'upUserId'=>$originUserId,
            'state'=>$distributionStateEnum->ON_ACCEPT
    	);
    	$response = $this->search($where, array());

    	foreach($response['data'] as $distribution){
            if( isset($this->path[$distribution['downUserId']]) )
                continue;
    		if( $this->dfs($distribution['downUserId'], $userId) == true)
                return true;
    	}

        //删除路径
        unset($this->path[$originUserId]);
        array_pop($this->result_path);
        return false;
=======
	if($originUserId == $userId){
		$this->result_path = $this->path;
		return;
	}

	$where = array(
		'upUserId'=>$originUserId,
        'state'=>$distributionStateEnum->ON_ACCEPT
	);
	$response = $this->search($where, array());
	$distributions = $response['data'];
	foreach($distributions as $distribution){
		$this->path[] = $distribution['downUserId'];
		$this->dfs($distribution['downUserId'], $userId);	
        unset($this[ count($this->path) - 1]);
	}
>>>>>>> master
    }

    public function getLink($originUserId, $userId){
        $this->path = array(); 
<<<<<<< HEAD
    	$this->result_path = array();
        $this->dfs($originUserId, $userId);
        return $this->result_path;
=======
	    $this->result = array();
        $this->path[] = $originUserId;
        $this->dfs($originUserId, $userId);
        return $this->result;
>>>>>>> master
    }
}

