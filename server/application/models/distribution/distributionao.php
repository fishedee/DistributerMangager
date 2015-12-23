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
        $this->load->model('user/userPermissionDb','userPermissionDb');
        $this->load->model('distribution/distributionQrCodeAo','distributionQrCodeAo');
        $this->load->model('order/orderAo','orderAo');
        $this->load->model('distribution/distributionOrderAo','distributionOrderAo');
        $this->load->model('user/userAppAo','userAppAo');
        //date:2015.11.27
        $this->load->model('distribution/distributionConfigAo','distributionConfigAo');
        $this->load->model('distribution/distributionConfigEnum','distributionConfigEnum');
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
        //date:2015.11.27
        $userInfo = $this->userAo->get($distribution['downUserId']);
        if($userInfo['clientId']){
            $clientInfo = $this->clientAo->get($distribution['vender'],$userInfo['clientId']);
            $distribution['nickName'] = base64_decode($clientInfo['nickName']);
            $distribution['headImgUrl'] = $clientInfo['headImgUrl'];
        }
        $distribution['upUserCompany'] = $this->userAo->get($distribution['upUserId'])['company'];
        $distribution['downUserCompany'] = $this->userAo->get($distribution['downUserId'])['company'];
        $distribution['distributionPercentShow'] = $this->getFixedPrice($distribution['distributionPercent']/100).'%';
        return $distribution;
    }

    public function search($where, $limit){
        if(isset($where['userId'])){
            $userId = $where['userId'];
            unset($where['userId']);
            //判断是否为厂家
            $result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
            if($result){
                $vender = 1;
            }else{
                $vender = 0;
            }
            $data = $this->distributionDb->search($where, $limit,$userId,$vender);
        }else{
            $data = $this->distributionDb->search($where,$limit);
        }
        if($data['data']){
            foreach($data['data'] as $key=>$value){
                $data['data'][$key] = $this->getOtherInfo($data['data'][$key]);
            }
        }
        // var_dump($data);die;
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
        // if($data['count'] != 0 )
        if($data['data'])
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
        if($userId != $distribution['vender']){
            if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
                throw new CI_MyException(1, "无权查询此非本用户的分成关系");
        }
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
        if($userId != $distribution['vender']){
            if($distribution['upUserId'] != $userId && $distribution['downUserId'] != $userId)
                throw new CI_MyException(1, "无权查询此非本用户的分成关系");
        }
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
        //判断是否为厂家
        $result = $this->userPermissionDb->checkPermissionId($upUserId,$this->userPermissionEnum->VENDER);
        $userInfo = $this->userAo->get($downUserId);
        $data = array(
            'upUserId'=>$upUserId,
            'downUserId'=>$downUserId,
            'state'=>$this->distributionStateEnum->ON_REQUEST,
            'phone'=>$userInfo['phone'],
        );
        if($result){
            //查找分销配置
            $config = $this->distributionConfigAo->getConfig($upUserId);
            $percent= 0;
            if($config == 0 || $config['distribution'] == $this->distributionConfigEnum->COMMON){
                //普通分销
                $percent = $config['agentFall'] * 100;
            }else{
                //特殊分销
                $percent = $config['highFall'] * 100;
            }
            //查找line
            $line = $this->distributionDb->checkLine($upUserId);
            $line = $line[0]["MAX(line)"];
            //厂家
            $data['remark'] = '1级代理商';
            $data['scort']  = 1;
            $data['distributionPercent'] = $percent;
            if($line){
                $data['line'] = $line + 1;
            }else{
                $data['line'] = 1;
            }
            $data['vender']  = $upUserId;
        }else{
            //非厂家
            // $upUserIdInfo = $this->distributionDb->getUpUserInfo($upUserId);
        }
        if(strstr($_SERVER['HTTP_HOST'], $upUserId)){
            $data['shopUrl'] = 'http://'.$_SERVER['HTTP_HOST'].'/'.$downUserId.'/item.html';
        }else{
            $data['shopUrl'] = 'http://'.$upUserId.'.'.$_SERVER['HTTP_HOST'].'/'.$downUserId.'/item.html';
        }
        return $this->distributionDb->add($data); 
    }

    public function accept($userId,$distributionId){
        $distribution = $this->get($userId, $distributionId);
        if($distribution['upUserId'] != $userId)
            throw new CI_MyException(1, "无权同意本用户的分成关系");
        $this->check($distribution['upUserId'], $distribution['downUserId'] , 1);
        if(preg_match('/^http:\/\/\d+\.[^\/]+\/\d+\/item\.html$/',$distribution['shopUrl']) == 0 )
            throw new CI_MyException(1, "请设置正确的商城URL");

        $this->distributionQrCodeAo->createQrCode($userId,$distribution);
        $result = $this->distributionDb->mod($distributionId,array(
            'state'=>$this->distributionStateEnum->ON_ACCEPT
        ));
        // if($result){
        //     //发送邮件
        //     $downUserId = $distribution['downUserId'];
        //     $userInfo   = $this->userAo->get($downUserId);
        //     $arr['user'] = $userInfo['email'];
        //     $arr['name'] = $userInfo['company'] ? $userInfo['company'] : '贵公司';
        //     $address[] = $arr;
        //     $title      = '分销商申请已经通过';
        //     $content    = '已经通过您的分销商申请,系统信件不用回复';
        //     $this->load->library('MyEmail','','email');
        //     $this->email->send($address,$title,$content);
        // }
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
    public function bind($userId,$clientId,$username,$password,$code){
        if($code != $this->session->userdata('phone_code')){
            throw new CI_MyException(1,'验证码错误');
        }
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
                $this->session->unset_userdata('phone_code');
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
        $distributionId = $this->request($userId, $askUserId);
        $distribution = $this->get($userId,$distributionId);
        return $this->distributionQrCodeAo->createQrCode($userId,$distribution);
    }

    //手机端分销商申请 没有账号 进行注册
    public function askReg($upUserId,$data){
        //判断有无账号
        $clientId = $data['clientId'];
        //先判断有无分配账号
        $result = $this->userAo->checkUserClientId($clientId);
        if($result){
            //判断有无分成关系
            $downUserId = $result[0]['userId'];
            $result = $this->distributionDb->checkHasDistribution($upUserId,$downUserId);
            if($result){
                throw new CI_MyException(1,'您已经建立分成关系');
            }else{
                throw new CI_MyException(1,'您已经分配好账号密码');
            }
        }else{
            $downUserId = $this->userAo->add($data);
            if(!$downUserId){
                throw new CI_MyException(1, "账号申请失败");
            }
            $userInfo['zhanghao'] = $data['name'];
            $userInfo['mima']     = $data['password'];
            return $this->ask($upUserId,$userInfo);
        }
    }

    //扫描二维码
    public function scanAskReg($upUserId,$data){
        $downUserId = $this->userAo->add($data);
        if(!$downUserId){
            throw new CI_MyException(1, "账号申请失败");
        }
        $userInfo['zhanghao'] = $data['name'];
        $userInfo['mima']     = $data['password'];
        return $this->ask($upUserId,$userInfo);
    }

    //判断有无上级
    public function judgeUp($upUserId){
        $result = $this->distributionDb->judgeUp($upUserId);
        return $result;
    }

    /**
     * 扫描自动成为分销商
     * openId:申请的微信用户
     * vender:厂家
     * upUserId:上线用户
     * line:所属分销队伍
     */
    public function qrCodeAsk($openId,$vender,$upUserId,$line){
        $this->load->model('user/userTypeEnum','userTypeEnum');
        $config = $this->distributionConfigAo->getConfig($vender);
        //先根据openid查询clientId
        $client['userId'] = $vender;
        $client['type']   = 2;
        $client['openId'] = $openId;
        $clientId = $this->clientAo->addOnce($client);
        //根据clientId 查询是否绑定用户
        $result = $this->userAo->checkUserClientId($clientId);
        if($result){
            //有这个用户
            $downUserId = $result[0]['userId'];
        }else{
            //没有 系统分配用户
            $userInfo = $this->userAo->get($vender);
            $username = 'weiyd'.$userInfo['name'].$userInfo['distributionNum'];
            $password = '123456';
            $data['name'] = $username;
            $data['password'] = $password;
            $data['password2']= $password;
            $data['company']  = $client['nickName'].'(系统自动分配)';
            $data['telephone'] = '0000';
            $data['phone'] = '00000000000';
            $data['type']  = $this->userTypeEnum->CLIENT;
            $data['clientId'] = $clientId;
            //非登录用户只能添加商城用户
            unset($data['permission']);
            //非登陆用户，注册账号有普通商城和普通分销权限
            $data['permission']=array(2,3);
            unset($data['client']);
            $downUserId = $this->userAo->add($data);
            if($downUserId){
                //更新
                $data = array();
                $data['distributionNum'] = $userInfo['distributionNum'] + 1;
                $this->userAo->mod($vender,$data);
            }else{
                $content = "系统分配账号密码失败";
                return $content;die;
            }
        }
        //判断这个用户有无上线
        $result = $this->checkUp($vender,$downUserId);
        $userAppInfo = $this->userAppAo->get($vender);
        if($result){
            //有上线 不能再申请
            $hasUpUserId = $result[0]['upUserId'];
            $hasUpUserName = $this->userAo->getUserName($hasUpUserId);
            $content = "您已经有推介人:".$hasUpUserName.",不能继续申请。".$config['intoCue'];
            return $content;die;
        }else{
            //没上线 申请 首先查询上线信息
            $distribution = $this->getUp($vender,$upUserId);
            if(!$distribution){
                $content = "分成信息出错";
                return $content;die;
            }else{
                $percent= 0;
                if($config){
                    $percent = $config['distributionFall'] * 100;
                }else{
                    $percent = 1000;
                }
                $line = $distribution[0]['line'];
                $scort= $distribution[0]['scort'] + 1;
                $data = array(
                    'upUserId'=>$upUserId,
                    'downUserId'=>$downUserId,
                    'state'=>$this->distributionStateEnum->ON_ACCEPT,
                    'phone'=>'00000000000',
                    'remark'=>$scort.'级分销商',
                    'distributionPercent'=>$percent,
                    'line'=>$line,
                    'scort'=>$scort,
                    'vender'=>$vender,
                    'parent_id'=>$distribution[0]['distributionId'],
                    'shopUrl'=>'http://'.$upUserId.'.'.$_SERVER['HTTP_HOST'].'/'.$downUserId.'/item.html'
                );
                $result = $this->distributionDb->add($data);
                if($result){
                    //创建二维码
                    $distribution = $this->getDistribution($result);
                    if(!$distribution){
                        $content = "获取分成关系失败";
                        return $content;die;
                    }
                    $distribution = $distribution[0];
                    $hasUpUserName = $this->userAo->getUserName($distribution['upUserId']);
                    // $this->distributionQrCodeAo->createQrCode($vender,$distribution);  //创建永久二维码
                    $this->distributionQrCodeAo->createLimitQrcode($vender,$distribution); //创建临时二维码
                    //为上级增加积分
                    $upUserInfo = $this->userAo->get($upUserId);
                    $upUserClientId = $upUserInfo['clientId'];
                    $this->load->model('client/scoreAo','scoreAo');
                    $this->scoreAo->askDistribution($vender,$upUserClientId);
                    // $content = "恭喜你成为".$hasUpUserName."的分销商.您的账号是:".$username.",密码:".$password."。".$config['intoCue'];
                    $content = "恭喜您成为".$userAppInfo['appName']."的代言人,您的推介人是".$hasUpUserName.",您的账号是:".$username.",密码:".$password."。".$config['intoCue'];
                    //推送客服消息
                    $upClientInfo = $this->clientAo->get($vender,$upUserClientId);
                    $upOpenId     = $upClientInfo['openId'];
                    $this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
                    $weixinSubscribe = $this->wxSubscribeAo->search($vender,array('remark'=>'新朋友'),'')['data'][0];
                    $weixinSubscribeId = $weixinSubscribe['weixinSubscribeId'];
                    $graphic=$this->wxSubscribeAo->graphicSearch($vender,$weixinSubscribeId);
                    // var_dump($graphic);die;
                    $this->load->model('user/userAppAo','userAppAo');
                    $info   = $this->userAppAo->getTokenAndTicket($vender);
                    $access_token = $info['appAccessToken'];
                    $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
                    $arr['touser'] = $upOpenId;
                    $arr['msgtype']= 'news';
                    $data = array();
                    $data['title'] = urlencode($graphic[0]['Title']);
                    $data['description'] = urlencode($graphic[0]['Description']);
                    $data['url']   = 'http://'.$vender.'.'.$_SERVER["HTTP_HOST"].'/'.$vender.'/distribution/center.html';
                    $data['picurl']= 'http://'.$_SERVER["HTTP_HOST"].$graphic[0]['PicUrl'];
                    $arr['news']['articles'][] = $data;
                    $this->load->library('http');
                    $httpResponse = $this->http->ajax(array(
                        'url'=>$url,
                        'type'=>'post',
                        'data'=>urldecode(json_encode($arr)),
                        'dataType'=>'plain',
                        'responseType'=>'json'
                    ));
                    return $content;die;
                }else{
                    $content = "分成建立失败";
                    return $content;die;
                }
            }
        }
    }

    //判断openid有无账号
    public function checkUserClientId2($vender,$openId){
        $this->load->model('user/userTypeEnum','userTypeEnum');
        $config = $this->distributionConfigAo->getConfig($vender);
        //先根据openid查询clientId
        $client['userId'] = $vender;
        $client['type']   = 2;
        $client['openId'] = $openId;
        $clientId = $this->clientAo->addOnce($client);
        //根据clientId 查询是否绑定用户
        $result = $this->userAo->checkUserClientId($clientId);
        if($result){
            //有账号
            $userId = $result[0]['userId'];
        }else{
            //无账号 分配
            $userInfo = $this->userAo->get($vender);
            $username = 'weiyd'.$userInfo['name'].$userInfo['distributionNum'];
            $password = '123456';
            $data['name'] = $username;
            $data['password'] = $password;
            $data['password2']= $password;
            $data['company']  = $client['nickName'].'(系统自动分配)';
            $data['telephone'] = '0000';
            $data['phone'] = '00000000000';
            $data['type']  = $this->userTypeEnum->CLIENT;
            $data['clientId'] = $clientId;
            //非登录用户只能添加商城用户
            unset($data['permission']);
            //非登陆用户，注册账号有普通商城和普通分销权限
            $data['permission']=array(2,3);
            unset($data['client']);
            $downUserId = $this->userAo->add($data);
            if($downUserId){
                //更新
                $data = array();
                $data['distributionNum'] = $userInfo['distributionNum'] + 1;
                $this->userAo->mod($vender,$data);
                
                $userAppInfo = $this->userAppAo->get($vender);
                $content = "恭喜您成为".$userAppInfo['appName']."下的一名会员,您的账号是:".$username.',密码是:'.$password."。".$config['subCue'];
                return $content;die;
            }else{
                $content = "系统分配账号密码失败";
                return $content;die;
            }
        }
    }

    //获取上线信息
    public function getUp($vender,$upUserId){
        return $this->distributionDb->getUp($vender,$upUserId);
    }

    //判断有无上线
    public function checkUp($vender,$downUserId){
        return $this->distributionDb->checkUp($vender,$downUserId);
    }

    //判断有无建立分成关系
    public function checkHasDistribution($ToUserName,$openId){
        //获取厂家id
        $vender = $this->userAppAo->getUserId($ToUserName);
        //获取clientId
        $client['userId'] = $vender;
        $client['type']   = 2;
        $client['openId'] = $openId;
        $clientId = $this->clientAo->addOnce($client);
        //先判断有无分配账号
        $result = $this->userAo->checkUserClientId($clientId);
        if($result){
            //判断有无分成关系
            $downUserId = $result[0]['userId'];
            $result = $this->distributionDb->checkHasDistribution($vender,$downUserId);
            if($result){
                return TRUE;
            }else{
                return FALSE;
            }
        }else{
            return FALSE;
        }
    }

    //获取分成关系
    public function getDistribution($distributionId){
        return $this->distributionDb->getDistribution($distributionId);
    }

    //我的盟友
    public function myAllies($vender,$myUserId){
        $result = $this->distributionDb->myAllies($vender,$myUserId);
        $sum = $result['first'] + $result['second'];
        return array($sum,$result['first'],$result['second']);
    }

    //获取我的盟友信息
    public function getAllies($vender,$myUserId,$allies){
        $info = $this->distributionDb->getAllies($vender,$myUserId,$allies);
        $data = $info['data'];
        foreach ($data as $key => $value) {
            $clientId = $this->userAo->getClientIdFromUser($value['downUserId']);
            $clientInfo = $this->clientAo->get($vender,$clientId);
            $data[$key]['nickName'] = base64_decode($clientInfo['nickName']);
            $data[$key]['headImgUrl'] = $clientInfo['headImgUrl'];

            //计算销售数量
            $data[$key]['num'] = $this->orderAo->getOrderNum($vender,$value['downUserId']);

            //计算分成
            $fall = $this->distributionOrderAo->getDistributionPrice($vender,$value['downUserId'])['fall'];
            $data[$key]['fall'] = $fall;

        }
        $info['data'] = $data;
        return $info;
    }

    //获得一级代理商
    public function getTopAllies($vender,$allies){
        $info = $this->distributionDb->getTopAllies($vender,$allies);
        $data = $info['data'];
        foreach ($data as $key => $value) {
            $clientId = $this->userAo->getClientIdFromUser($value['downUserId']);
            $clientInfo = $this->clientAo->get($vender,$clientId);
            $data[$key]['nickName'] = base64_decode($clientInfo['nickName']);
            $data[$key]['headImgUrl'] = $clientInfo['headImgUrl'];

            //计算销售数量
            $data[$key]['num'] = $this->orderAo->getOrderNum($vender,$value['downUserId']);

            //计算分成
            $fall = $this->distributionOrderAo->getDistributionPrice($vender,$value['downUserId'])['fall'];
            $data[$key]['fall'] = $fall;

        }
        $info['data'] = $data;
        return $info;
    }

    private $distributionLink = array();

    private function dfs2($vender,$userId){
        // var_dump(1);die;
        if($vender == $userId){
            return true;
        }
        //获取id是第几级分销
        $result = $this->distributionDb->getScort($vender,$userId);
        if(!$result){
            return false;
        }
        //获取一级代理商
        $line = $result[0]['line'];
        $oneInfo = $this->distributionDb->getOneScort($vender,$line);
        $this->distributionLink[0]['distributionId'] = $oneInfo[0]['distributionId'];
        $this->distributionLink[0]['upUserId']       = $oneInfo[0]['upUserId'];
        $this->distributionLink[0]['downUserId']     = $oneInfo[0]['downUserId'];
        $scort = $result[0]['scort'];
        if($scort > 3){
            //获取上一级信息
            $twoInfo = $this->distributionDb->checkUp($vender,$result[0]['upUserId']);
            if(!$twoInfo){
                throw new CI_MyException(1,'无效二级以上分销');
            }
            //获取再上一级信息
            $threeInfo = $this->distributionDb->checkUp($vender,$twoInfo[0]['upUserId']);
            $this->distributionLink[1]['distributionId']   = $twoInfo[0]['distributionId'];
            $this->distributionLink[1]['upUserId']         = $twoInfo[0]['upUserId'];
            $this->distributionLink[1]['downUserId']       = $result[0]['upUserId'];
            $this->distributionLink[2]['distributionId']   = $threeInfo[0]['distributionId'];
            $this->distributionLink[2]['upUserId']         = $threeInfo[0]['upUserId'];
            $this->distributionLink[2]['downUserId']       = $twoInfo[0]['upUserId'];
            $this->distributionLink[3]['distributionId']   = $result[0]['distributionId'];
            $this->distributionLink[3]['upUserId']         = $result[0]['upUserId'];
            $this->distributionLink[3]['downUserId']       = $result[0]['downUserId'];
        }elseif($scort == 3){
            //获取上一级信息
            $twoInfo = $this->distributionDb->checkUp($vender,$result[0]['upUserId']);
            if(!$twoInfo){
                throw new CI_MyException(1,'无效二级以上分销');
            }
            $this->distributionLink[1]['distributionId']   = $twoInfo[0]['distributionId'];
            $this->distributionLink[1]['upUserId']         = $twoInfo[0]['upUserId'];
            $this->distributionLink[1]['downUserId']       = $result[0]['upUserId'];
            $this->distributionLink[2]['distributionId']   = $result[0]['distributionId'];
            $this->distributionLink[2]['upUserId']         = $result[0]['upUserId'];
            $this->distributionLink[2]['downUserId']       = $result[0]['downUserId'];
        }elseif($scort == 2){
            $this->distributionLink[1]['distributionId']   = $result[0]['distributionId'];
            $this->distributionLink[1]['upUserId']         = $result[0]['upUserId'];
            $this->distributionLink[1]['downUserId']       = $result[0]['downUserId'];
        }
        return true;
    }

    // 获取可获分成的user
    public function getLinks($vender,$userId){
        $this->dfs2($vender,$userId);
        return $this->distributionLink;
    }

    //检测我的等级
    public function checkMyDegree($vender,$myUserId){
        $result = $this->distributionDb->checkMyDegree($vender,$myUserId);
        if($result){
            return 0;
        }else{
            return 1;
        }
    }

    //升级总代理
    public function upgrade($vender,$distributionId){
        $distribution = $this->get($vender,$distributionId);
        $line = $distribution['line'];
        //获取最大跟最小
        // $info = $this->distributionDb->getMaxAndMin($vender,$line);
        // $max  = $info[0]['MAX[scort]'];
        // $min  = $info[0]['MAX[scort]'];
        if($distribution['scort'] == 1){
            throw new CI_MyException(1,'已经是一级代理商');
        }
        $after = $this->distributionDb->getAfter($vender,$distribution['scort'],$line);
        $line  = $this->distributionDb->checkLine($vender);
        $line = $line[0]["MAX(line)"];
        $scort = 1;
        foreach ($after as $key => $value) {
            if($key == 0){
                $data['line'] = $line + 1;
                $data['scort']= $scort;
                $data['distributionPercent'] = 800;
                $data['remark'] = $scort.'级代理商';
            }else{
                $data['line'] = $line + 1;
                $data['scort']= $scort;
                $data['distributionPercent'] = 1000;
                $data['remark'] = $scort.'级代理商';
            }
            $this->distributionDb->mod($value['distributionId'],$data);
            $scort++;
        }
        return 1;
    }

    //推荐总代
    public function recommend($vender,$distributionId){
        $distribution = $this->get($vender,$distributionId);
        if($distribution['scort'] != 1){
            throw new CI_MyException(1,'只能推荐总代');
        }
        if($distribution['recommend'] == 1){
            $data['recommend'] = 0;
        }else{
            $data['recommend'] = 1;
        }
        return $this->distributionDb->mod($distributionId,$data);
    }

    //获取推荐信息
    public function getRecommend($userId){
        $info = $this->distributionDb->getRecommend($userId);
        if($info){
            $arr = array();
            foreach ($info as $key => $value) {
                $userInfo = $this->userAo->get($value['downUserId']);
                $data = array();
                $data['company'] = $userInfo['company'];
                // $data['company'] = 1;
                $clientInfo = $this->clientAo->get($userId,$userInfo['clientId']);
                $data['img'] = $clientInfo['headImgUrl'];
                $data['nickName'] = $clientInfo['nickName'];
                $data['url'] = 'http://'.$userId.'.'.$_SERVER[HTTP_HOST].'/'.$userId.'/distribution/myqrcode.html?myUserId='.$value['downUserId'];
                $arr[] = $data;
            }
            return $arr;
        }else{
            return 0;
        }
    }

    //获取商家信息
    public function getBusinessInfo($userId,$clientId){
        $userInfo = $this->userAo->get($userId);
        $clientInfo = $this->clientAo->get($userId,$clientId);
        if($userInfo['openId'] != $clientInfo['openId']){
            throw new CI_MyException(1,'非法登陆');
        }
        //获取应付佣金
        $needPay  = $this->distributionOrderAo->getNeedPay($userId);
        //获取下线各级人数
        $maxScort = $this->distributionDb->getMaxScort($userId);
        $maxScort = $maxScort[0]['MAX(scort)'];
        $down = array();
        $sumPeople = 0;
        for ($i=1; $i < $maxScort+1; $i++) { 
            $down[$i] = $this->distributionDb->getScortNum($userId,$i);
            $sumPeople += $down[$i];
        }
        //获取有多少提现需要处理
        $this->load->model('withdraw/withDrawAo','withDrawAo');
        $withDrawNum = $this->withDrawAo->getNeedHandleNum($userId,0);
        $arr['name'] = $userInfo['name'];
        $arr['score']    = $userInfo['score'];
        $arr['needPay']  = sprintf('%.2f',$needPay/100);
        $arr['sumPeople']= $sumPeople;
        $arr['down']     = $down;
        $arr['withDrawNum'] = $withDrawNum;
        return $arr;
    }

    /**
     * 查询会员
     * date:2015.11.27
     */
    public function searchMember($userId,$where,$limit){
        //判断是否为厂家
        $result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
        if(!$result){
            throw new CI_MyException(1,'该菜单只适用于厂家');
        }
        //检测特殊分销权限
        $config = $this->distributionConfigAo->getConfig($userId);
        if($config == 0 || $config['distribution'] == $this->distributionConfigEnum->COMMON){
            throw new CI_MyException(1,'该菜单只适用于特殊分销');
        }
        $where['vender'] = $userId;
        $data = $this->distributionDb->searchMember($where,$limit);
        if($data['data']){
            foreach ($data['data'] as $key => $value) {
                $data['data'][$key] = $this->getOtherInfo($data['data'][$key]);
            }
        }
        return $data;
    }

    /**
     * 升级高级会员
     * date:2015.11.27
     */
    public function upgradeMember($userId,$distributionId){
        //判断是否为厂家
        $result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
        if(!$result){
            throw new CI_MyException(1,'非厂家无权操作');
        }
        //检测特殊分销权限
        $config = $this->distributionConfigAo->getConfig($userId);
        if($config == 0 || $config['distribution'] == $this->distributionConfigEnum->COMMON){
            throw new CI_MyException(1,'该操作仅限于特殊分销');
        }
        $distribution = $this->get($userId,$distributionId);
        if($distribution['member'] == 1){
            throw new CI_MyException(1,'该会员已经是高级会员,不需要再去升级');
        }
        $data['member'] = 1;
        return $this->distributionDb->mod($distributionId,$data);
    }

    /**
     * 降级普通会员
     * date:2015.11.27
     */
    public function degradeMember($userId,$distributionId){
        //判断是否为厂家
        $result = $this->userPermissionDb->checkPermissionId($userId,$this->userPermissionEnum->VENDER);
        if(!$result){
            throw new CI_MyException(1,'非厂家无权操作');
        }
        //检测特殊分销权限
        $config = $this->distributionConfigAo->getConfig($userId);
        if($config == 0 || $config['distribution'] == $this->distributionConfigEnum->COMMON){
            throw new CI_MyException(1,'该操作仅限于特殊分销');
        }
        $distribution = $this->get($userId,$distributionId);
        if($distribution['member'] == 0){
            throw new CI_MyException(1,'该会员已经是普通会员,不需要再去降级');
        }
        $data['member'] = 0;
        return $this->distributionDb->mod($distributionId,$data);
    }

    /**
     * 获取用户分成关系
     * date:2015.11.30
     */
    public function getDistributionUser($vender,$downUserId){
        $result = $this->distributionDb->getDistributionUser($vender,$downUserId);
        if($result){
            return $result[0];
        }else{
            throw new CI_MyException(1,'分成关系出错');
        }
    }

    /**
     * date:2015.12.1
     */
    private function dfs3($vender,$entranceUserId){
        //获取分销配置
        $config = $this->distributionConfigAo->getConfig($vender);
        //获取是第几级分销
        $result = $this->distributionDb->getScort($vender,$entranceUserId);
        $scort  = $result[0]['scort'];
        if(!$result){
            return false;
        }
        //获取多少级有分销提成
        $distributionNum = $config['distributionNum'];
        if($distributionNum == 1){
            //总代
            $line = $result[0]['line'];
            $oneInfo = $this->distributionDb->getOneScort($vender,$line);
            $this->distributionLink[0]['distributionId'] = $oneInfo[0]['distributionId'];
            $this->distributionLink[0]['upUserId']       = $oneInfo[0]['upUserId'];
            $this->distributionLink[0]['downUserId']     = $oneInfo[0]['downUserId'];
            $this->distributionLink[0]['distributionPercent'] = $config['agentFall'];
        }else{
            $num = $distributionNum - 1;
            //总代
            $line = $result[0]['line'];
            $oneInfo = $this->distributionDb->getOneScort($vender,$line);
            $this->distributionLink[0]['distributionId'] = $oneInfo[0]['distributionId'];
            $this->distributionLink[0]['upUserId']       = $oneInfo[0]['upUserId'];
            $this->distributionLink[0]['downUserId']     = $oneInfo[0]['downUserId'];
            $this->distributionLink[0]['distributionPercent'] = $config['agentFall'];
            $scort--;
            for ($i=0; $i < $num; $i++) {
                if($scort){
                    if($i == 0){
                        //第一级
                        $this->distributionLink[$i+1]['distributionId'] = $result[0]['distributionId'];
                        $this->distributionLink[$i+1]['upUserId']     = $result[0]['upUserId'];
                        $this->distributionLink[$i+1]['downUserId']   = $result[0]['downUserId'];
                        $this->distributionLink[$i+1]['distributionPercent'] = $config['one'];
                        $scort--;
                    }elseif($i > 0 && $i < 3){
                        //第二级
                        $result = $this->distributionDb->getScort($vender,$this->distributionLink[$i]['upUserId']);
                        $this->distributionLink[$i+1]['distributionId'] = $result[0]['distributionId'];
                        $this->distributionLink[$i+1]['upUserId']     = $result[0]['upUserId'];
                        $this->distributionLink[$i+1]['downUserId']   = $result[0]['downUserId'];
                        if($i == 1){
                            $this->distributionLink[$i+1]['distributionPercent'] = $config['two'];
                        }elseif($i == 2){
                            $this->distributionLink[$i+1]['distributionPercent'] = $config['three'];
                        }
                        $scort--;
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }
            }
        }
        return true;
    }

    private function dfs4($vender,$entranceUserId){
        //获取分销配置
        $config = $this->distributionConfigAo->getConfig($vender);
        //获取分成关系
        $result = $this->distributionDb->getScort($vender,$entranceUserId);
        $scort  = $result[0]['scort'];
        $scoreNum = $config['scoreNum'];
        for ($i=0; $i < $scoreNum; $i++) { 
            if($scort){
                if($i == 0){
                    //第一级
                    $this->scoreLink[$i]['userId'] = $result[0]['downUserId'];
                    $this->scoreLink[$i]['score']  = $config['commonDownScore'] ? $config['commonDownScore'] : 0;
                    $userInfo = $this->userAo->get($result[0]['downUserId']);
                    $this->scoreLink[$i]['clientId'] = $userInfo['clientId'];
                    $scort--;
                }else{
                    $result = $this->distributionDb->getScort($vender,$this->scoreLink[$i-1]['userId']);
                    $this->scoreLink[$i]['userId'] = $result[0]['upUserId'];
                    $this->scoreLink[$i]['score']  = $config['commonUpScore'] ? $config['commonUpScore'] : 0;
                    $userInfo = $this->userAo->get($result[0]['upUserId']);
                    $this->scoreLink[$i]['clientId'] = $userInfo['clientId'];
                    $scort--;
                }
            }else{
                return false;
            }
        }
    }

    private $scoreLink = array();

    //分成关系
    public function getLinks2($vender,$userId){
        $this->dfs3($vender,$userId);
        return $this->distributionLink;
    }

    //积分关系
    public function getScoreLinks($vender,$entranceUserId){
        $this->dfs4($vender,$entranceUserId);
        return $this->scoreLink;
    }

    /**
     * 获取分成id
     * date:2015.12.08
     */
    public function getDistributionId($vender,$downUserId){
        $info = $this->distributionDb->getDistributionId($vender,$downUserId);
        return $info['distributionId'];
    }
}
