<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionAo extends CI_Model
{
    public function __construct(){
        parent::__construct();
	    $this->load->model('distribution/distributionDb', 'distributionDb');
        $this->load->model('distribution/distributionstateEnum', 'distributionStateEnum');
        $this->load->model('user/userAo','userAo');
        $this->load->model('client/clientAo','clientAo');
        $this->load->model('user/loginAo','loginAo');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    private function getOtherInfo($distribution){
        $distribution['shopUrl'] = preg_replace(
            '/http:\/\/(\d+)\.([^\/]+)\/\d+\/item\.html/',
            'http://${1}.'.$_SERVER['HTTP_HOST'].'/'.$distribution['downUserId'].'/item.html',
            $distribution['shopUrl']
        );
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

    public function getAcceptLinkNum($upUserId){
        return $this->search(
            array('upUserId'=>$upUserId,'state'=>$this->distributionStateEnum->ON_ACCEPT),
            array()
        )['count'];
    }
    private function checkSingleTree($distributionMap,&$isVisit,$topUserId,$userId){
        if( isset($isVisit[$userId])){
            $link = $this->getLink($topUserId,$userId);
            throw new CI_MyException(1,'在'.$topUserId.'->'.$userId.'之间已经有一条路径，禁止再增加分成关系<br/>原分成关系为：'.implode($link,'->'));
        }
        $isVisit[$userId] = true;

        if( isset($distributionMap[$userId]) == false )
            return;

        foreach($distributionMap[$userId] as $downUserId=>$temp ){
            $this->checkSingleTree($distributionMap,$isVisit,$topUserId,$downUserId);
        }

    }

    private function check($upUserId, $downUserId , $agree = 0){
        //校验是否有相同的边
        if($upUserId == $downUserId)
            throw new CI_MyException(1,'禁止建立自己指向自己的分成关系');

        //校验是否有相同的边
        $data = $this->distributionDb->search(array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
            'state'=>$this->distributionStateEnum->ON_ACCEPT
        ),array());
        if($data['count'] != 0 )
            throw new CI_MyException(1,'已经存在'.$upUserId.'->'.$downUserId.'之间的分成关系，请勿重复添加');

        if($agree == 0){
            $data = $this->distributionDb->search(array(
                'upUserId'=>$upUserId,
                'downUserId'=>$downUserId,
                'state'=>$this->distributionStateEnum->ON_REQUEST
            ),array());
            if($data['count'] != 0 )
                throw new CI_MyException(1,'您的申请已经提交，请耐心等候工作人员处理');
        }

        //取出所有边的数据
        $data = $this->distributionDb->search(array(
            'state'=>$this->distributionStateEnum->ON_ACCEPT
        ),array());

        //建立分成关系映射图
        $distributionMap = array();
        foreach($data['data'] as $key=>$value )
            $distributionMap[$value['upUserId']][$value['downUserId']] = true;   
        $distributionMap[$upUserId][$downUserId] = true;
        
        //遍历分成关系顶端，计算是否两点之间有多条路径
        foreach($distributionMap as $topUserId=>$temp ){
            $isVisit = array();
            $this->checkSingleTree($distributionMap,$isVisit,$topUserId,$topUserId);
        }
        return 0;
    }

    public function get($userId, $distributionId){
        $distribution = $this->distributionDb->get($distributionId);
        if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
            throw new CI_MyException(1, "无权查询此非本用户的分成关系");
         
        $distribution = $this->getOtherInfo($distribution);
        return $distribution;
    }

    public function getByLink($userId,$upUserId,$downUserId){
         $data = $this->distributionDb->search(array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
        ),array());
        if($data['count'] == 0 )
            throw new CI_MyException(1,'不存在该分成关系');

        $distribution = $data['data'][0];
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
        $userInfo = $this->userAo->get($downUserId);
        $this->distributionDb->add(array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
            'state'=>$this->distributionStateEnum->ON_REQUEST,
            'phone'=>$userInfo['phone'],
        )); 
    }

    public function accept($userId,$distributionId){
        $distribution = $this->get($userId, $distributionId);
        if($distribution['upUserId'] != $userId)
            throw new CI_MyException(1, "无权同意本用户的分成关系");
        $this->check($distribution['upUserId'], $distribution['downUserId'] , 1);
        if(preg_match('/^http:\/\/\d+\.[^\/]+\/\d+\/item\.html$/',$distribution['shopUrl']) == 0 )
            throw new CI_MyException(1, "请设置正确的商城URL");

        $result = $this->distributionDb->mod($distributionId,array(
            'state'=>$this->distributionStateEnum->ON_ACCEPT
        ));
        if($result){
            //发送邮件
            $downUserId = $distribution['downUserId'];
            $userInfo   = $this->userAo->get($downUserId);
            $arr['user'] = $userInfo['email'];
            $arr['name'] = $userInfo['company'] ? $userInfo['company'] : '贵公司';
            $address[] = $arr;
            $title      = '分销商申请已经通过';
            $content    = '已经通过您的分销商申请,系统信件不用回复';
            $this->load->library('MyEmail','','email');
            $this->email->send($address,$title,$content);
        }
    }

    public function modPrecent($userId,$distributionId,$distributionPercentShow,$shopUrl){
        $distribution = $this->get($userId, $distributionId);
        if($distribution['upUserId'] != $userId)
            throw new CI_MyException(1, "无权同意本用户的分成关系");

        $distributionPercent = intval(floatval($distributionPercentShow)*100);
        if($distributionPercent >= 10000)
            throw new CI_MyException(1, "默认分成比例不能大于100%");
        if($distributionPercent < 0)
            throw new CI_MyException(1, "默认分成比例不能少于0");
        if(preg_match('/^http:\/\/\d+\.[^\/]+\/\d+\/item\.html$/',$shopUrl) == 0 )
            throw new CI_MyException(1, "请输入正确的商城URL");

        $this->distributionDb->mod($distributionId,array(
            'distributionPercent'=>$distributionPercent,
            'shopUrl'=>$shopUrl
        ));
    }

    private $path = array();
    private $result = array();

    
    private function dfs($originUserId, $userId){
        //记录路径
        $this->path[$originUserId] = 1;
        $this->result_path[] = $originUserId;
    	if($originUserId == $userId){
    		return true;
    	}

        //遍历下级分类
    	$where = array(
    		'upUserId'=>$originUserId,
            'state'=>$this->distributionStateEnum->ON_ACCEPT
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
    }

    public function getLink($originUserId, $userId){
        $this->path = array(); 
    	$this->result_path = array();
        $this->dfs($originUserId, $userId);
        return $this->result_path;
    }

    //判断有无绑定
    public function judgeBind($userId,$clientId){
        $clientInfo = $this->clientAo->get($userId,$clientId);
        $openId     = $clientInfo['openId'];
        return $this->userAo->searchOpenId($openId);
    }

    //绑定
    public function bind($userId,$clientId,$username,$password){
        $this->loginAo->login($username,$password);
        if($this->session->userdata('userId')){
            $userInfo   = $this->userAo->get($this->session->userdata('userId'));
            if($userInfo['openId'] != NULL){
                throw new CI_MyException(1, "该账号已经绑定了");
            }
            $clientInfo = $this->clientAo->get($userId,$clientId);
            $openId     = $clientInfo['openId'];
            //验证openId 是否存在
            $result     = $this->userAo->searchOpenId($openId);
            if($result == FALSE){
                return $this->userAo->bind($this->session->userdata('userId'),$openId);
            }else{
               throw new CI_MyException(1, "该openId已经被绑定");
            }
        }else{
            throw new CI_MyException(1, "非法错误");
        }
    }

    //解绑
    public function unBind($userId,$clientId){
        $clientInfo = $this->clientAo->get($userId,$clientId);
        $openId     = $clientInfo['openId'];
        $result     = $this->userAo->searchOpenId($openId);
        if($result == FALSE){
            throw new CI_MyException(1, "该openId没有绑定");
        }else{
            return $this->userAo->unBind($result);
        }
    }

    //手机端查询
    public function mobileSearch($userId,$clientId,$data){
        $clientInfo = $this->clientAo->get($userId,$clientId);
        $openId     = $clientInfo['openId'];
        $result     = $this->userAo->searchOpenId($openId);
        if($result == FALSE){
            throw new CI_MyException(1, "该openId没有绑定");
        }else{
            //有绑定 可以开始查询
            if(isset($data['down'])){
                //查询供货商
                $where = array(
                    'downUserId'=>$result
                );
                return $this->search($where,array());
            }elseif(isset($data['up'])){
                //查询分销商
                $where = array(
                    'upUserId'=>$result
                    );
                return $this->search($where,array());
            }
        }
    }

    //手机端分销商申请
    public function ask($userId,$data){
        $username = $data['zhanghao'] ? $data['zhanghao'] : null;
        $password = $data['mima'] ? $data['mima'] : null;
        if(!$username){
            throw new CI_MyException(1, "请输入账号");
        }
        if(!$password){
            throw new CI_MyException(1, "请输入密码");
        }
        //根据账号 密码 获取 userId
        $hasPassword = $this->userAo->getPasswordHash($password);
        $askUserId   = $this->userAo->checkLoginInfo($username,$hasPassword,$password);
        if($userId == $askUserId){
            throw new CI_MyException(1, "自己不用申请");
        }
        return $this->request($userId, $askUserId);
    }

    //手机端分销商申请 没有账号 进行注册
    public function askReg($upUserId,$data){
        $downUserId = $this->userAo->add($data);
        if(!$downUserId){
            throw new CI_MyException(1, "账号申请失败");
        }
        $userInfo['zhanghao'] = $data['name'];
        $userInfo['mima']     = $data['password'];
        return $this->ask($upUserId,$userInfo);
    }
}

