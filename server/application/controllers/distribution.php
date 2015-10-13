<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Distribution extends CI_Controller
{
    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('client/clientLoginAo', 'clientLoginAo');
        $this->load->model('distribution/distributionAo', 'distributionAo');
        $this->load->model('distribution/distributionStateEnum', 'distributionStateEnum');
        $this->load->model('user/userPermissionEnum', 'userPermissionEnum');
        $this->load->model('distribution/distributionQrCodeAo','distributionQrCodeAo');
        $this->load->model('user/userTypeEnum','userTypeEnum');
        $this->load->library('argv', 'argv');
    }

    /**
     * @view json
     */
    public function getAllState(){
        return $this->distributionStateEnum->names;
    }

    /**
     * @view json
     */
    public function search(){
        //检查参数
        $dataWhere = $this->argv->checkGet(array(
            array('direction', 'require'),
            array('state', 'option')
        ));

        $dataLimit = $this->argv->checkGet(array(
            array('pageIndex', 'require'),
            array('pageSize', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];
        if($dataWhere['direction'] == 'down')
            $where = array(
                'upUserId'=>$user['userId']
            );
        else 
            $where = array(
                'downUserId'=>$user['userId']
            );
        if( isset($dataWhere['state']))
            $where['state'] = $dataWhere['state'];
        $where['userId'] = $userId;
        return $this->distributionAo->search($where, $dataLimit);
    }

    /**
     * @view json
     */
    public function get(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('distributionId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        return $this->distributionAo->get($user['userId'], $data['distributionId']);
    }

    /**
     * @view json
     */
    public function getByLink(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('upUserId', 'require'),
            array('downUserId', 'require'),
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];

        return $this->distributionAo->getByLink($userId,$data['upUserId'], $data['downUserId']);
    }

    /**
     * @view json
     */
    public function request(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('upUserId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        $this->distributionAo->request($data['upUserId'], $user['userId']);
    } 

    /**
     * @view json
     */
    public function accept(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require')
        ));
        
        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION_PRO
        );

        if( $this->distributionAo->getAcceptLinkNum($user['userId']) >= $user['downDistributionNum'])
            throw new CI_MyException(1,'您的帐号只能最多可以设置 '.$user['downDistributionNum'].' 个分销商');

        $this->distributionAo->accept($user['userId'],$data['distributionId']);
    }

    /**
     * @view json
     */
    public function mod(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require'),
            array('shopUrl', 'require'),
        ));
        $distributionId = $data['distributionId'];
        $shopUrl = $data['shopUrl'];

        $data = $this->argv->checkPost(array(
            array('distributionPercentShow', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );

        $this->distributionAo->modPrecent(
            $user['userId'],
            $distributionId,
            $data['distributionPercentShow'],
            $shopUrl
        );
    }

    /**
     * @view json
     */
    public function del(){
        //检查参数
        $data = $this->argv->checkPost(array(
            array('distributionId', 'require')
        ));

        //检查权限
        $user = $this->loginAo->checkMustClient(
            $this->userPermissionEnum->COMPANY_DISTRIBUTION
        );
        $userId = $user['userId'];

        $this->distributionAo->del($userId, $data['distributionId']);
    }

    /**
     * @view json
     */
    public function getLink(){
        //检查参数
        $data = $this->argv->checkGet(array(
            array('upUserId', 'require'),
            array('downUserId', 'require')
        ));

        return $this->distributionAo->getLink($data['upUserId'], $data['downUserId']);
    }

    /**
     * @view json
     * 判断有无绑定
     */
    public function judgeBind(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->judgeBind($userId,$clientId);
        }
    }

    /**
     * @view json
     * 绑定
     */
    public function bind(){
        if($this->input->is_ajax_request()){
            $username = $this->input->post('username');
            $password = $this->input->post('password');
            $code = $this->input->post('code');
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->bind($userId,$clientId,$username,$password,$code);
        }
    }

    /**
     * @view json
     * 解绑
     */
    public function unBind(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            return $this->distributionAo->unBind($userId,$clientId);
        }
    }

    /**
     * @view json
     * 手机端查询
     */
    public function mobileSearch(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $clientId = $this->session->userdata('clientId');
            $data   = $this->input->post();   //ajax 通过post方式提交过来的数据
            return $this->distributionAo->mobileSearch($userId,$clientId,$data);
        }
    }

    /**
     * @view json
     * 手机端分销商申请
     */
    public function ask(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $data = $this->input->post();
            return $this->distributionAo->ask($userId,$data);
        }
    }

    /**
     * @view json
     * 手机端分销商申请 注册
     */
    public function askReg(){
        if($this->input->is_ajax_request()){
            //检查输入参数
            $data = $this->argv->checkPost(array(
                array('name','require'),
                array('password','option','123456'),
                array('type','option',$this->userTypeEnum->CLIENT),
                array('phone','require'),
                array('telephone','option'),
                array('company','option'),
                array('email','require'),
                array('followLink','option|noxss'),
                array('downDistributionNum','option',0),
                array('permission','option',array()),
                array('client','option',array()),
            ));
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $upUserId = $argv['userId'];
            //检查权限
            $client = $this->clientLoginAo->checkMustLogin($argv['userId']);
            $clientId = $client['clientId'];
            // $data['company'] = base64_decode($client['nickName']).'(手机申请)';
            // $data['company']   = $data['company'];
            $data['telephone'] = '0000';
            $data['clientId']  = $clientId;
            //检查权限
            if($clientId){
                //非登录用户只能添加商城用户
                $data['type'] = $this->userTypeEnum->CLIENT;
                unset($data['permission']);
                //非登陆用户，注册账号有普通商城和普通分销权限
                $data['permission']=array(2,3);
                unset($data['client']);
            }else{
                throw new CI_MyException(1, "请用手机端登陆");
            }
            return $this->distributionAo->askReg($upUserId,$data);
        }
    }

    //测试发送邮件
    public function test(){
        $this->load->library('MyEmail','','email');
        $address = '330448219@qq.com';
        $title   = '测试发送邮件';
        $content = '测试发送邮件的内容';
        $this->email->send($address,$title,$content);
    }

    /**
     * @view json
     * 我的二维码
     */
    public function qrCode(){
        if($this->input->is_ajax_request()){
            $dataLimit = $this->argv->checkGet(array(
                array('pageIndex', 'require'),
                array('pageSize', 'require')
            ));
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $userId = $user['userId'];
            return $this->distributionQrCodeAo->qrCode($userId,$dataLimit);
        }
    }

    /**
     * @view json
     * 生成二维码
     */
    public function createQrCode(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $userId = $user['userId'];
            $className = __CLASS__;
            // $url    = $_SERVER['HTTP_HOST'].'/'.strtolower($className);
            $url = $_SERVER['HTTP_HOST'];
            return $this->distributionQrCodeAo->createQrCode($userId);
        }
    }

    /**
     * @view json
     * 获取二维码
     */
    public function getQrCode(){
        $argv = $this->argv->check(array(
            array('userId', 'require')
        ));
        $userId = $argv['userId'];
        return $this->distributionQrCodeAo->getQrCode($userId);
    }

    /**
     * @view json
     * 扫描二维码
     */
    public function qrAsk(){
        if($this->input->is_ajax_request()){
            $argv = $this->argv->check(array(
                array('userId', 'require')
            ));
            $userId = $argv['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->distributionQrCodeAo->qrAsk($userId,$clientId);
        }
    }

    /**
     * @view json
     * 判断有无上级
     */
    public function judgeUp(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $url  = $this->input->get('url');
            $url  = strrev($url);
            $downUserId = substr($url, strpos($url, '/')+1,5);
            $downUserId = strrev($downUserId);
            $upUserId   = $data['userId'];
            $topId  = $this->distributionAo->judgeUp($upUserId);
            $nowurl = $_SERVER['HTTP_HOST'];
            $nowurl = substr($nowurl,strpos($nowurl, '.')+1);
            $nowurl= 'http://'.$topId.'.'.$nowurl.'/'.$downUserId.'/item.html';
            return $nowurl;
        }
    }

    /**
     * @view json
     * 判断分享
     */
    public function judgeEnjoyer(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $url = $this->input->get('url');
            $url  = strrev($url);
            $downUserId = substr($url, strpos($url, '/')+1,5);
            $downUserId = strrev($downUserId);
            $upUserId   = $data['userId'];
            if($upUserId == $downUserId){
                return 0;
            }else{
                $this->load->model('user/userAo','userAo');
                $this->load->model('client/clientAo','clientAo');
                $this->load->model('user/userAppAo','userAppAo');
                $userInfo = $this->userAo->get($downUserId);
                $clientId = $userInfo['clientId'];
                $clientInfo = $this->clientAo->get($upUserId,$clientId);
                $userAppInfo = $this->userAppAo->get($downUserId);
                if($this->session->userdata('clientId') == $clientId){
                    return 0;
                }else{
                    $nickName = base64_decode($clientInfo['nickName']);
                    $headImgUrl = $clientInfo['headImgUrl'];
                    return array(
                        'nickName'=>$nickName,
                        'headImgUrl'=>$headImgUrl
                        );
                }
            }
        }
    }

    /**
     * @view json
     * 我的盟友
     */
    public function myAllies(){
        if($this->input->is_ajax_request()){
            $vender = $this->input->get('userId');
            $myUserId = $this->input->get('userIds');
            $client = $this->clientLoginAo->checkMustLogin($vender);
            $clientId = $client['clientId'];
            return $this->distributionAo->myAllies($vender,$myUserId);
        }
    }

    /**
     * @view json
     * 获取盟友信息
     */
    public function getAllies(){
        if($this->input->is_ajax_request()){
            $vender = $this->input->get('userId');
            $client = $this->clientLoginAo->checkMustLogin($vender);
            $allies = $this->input->get('allies');
            $myUserId = $this->input->get('myUserId');
            return $this->distributionAo->getAllies($vender,$myUserId,$allies);
        }
    }

    /**
     * @view json
     * 获取一级会员信息
     */
    public function getTopAllies(){
        if($this->input->is_ajax_request()){
            $vender = $this->input->get('userId');
            $myUserId = $this->input->get('myUserId');
            $client = $this->clientLoginAo->checkMustLogin($vender);
            $allies = $this->input->get('allies');
            return $this->distributionAo->getTopAllies($vender,$myUserId,$allies);
        }
    }


    /**
     * @view json
     * 检测我目前的等级
     */
    public function checkMyDegree(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $vender = $data['userId'];
            $myUserId = $this->input->get('myUserId');
            return $this->distributionAo->checkMyDegree($vender,$myUserId);
        }
    }

    /**
     * @view json
     * 升级总代理
     */
    public function upgrade(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $vender = $user['userId'];
            $distributionId = $this->input->post('distributionId');
            return $this->distributionAo->upgrade($vender,$distributionId);
        }
    }

    /**
     * @view json
     * 推荐总代
     */
    public function recommend(){
        if($this->input->is_ajax_request()){
            //检查权限
            $user = $this->loginAo->checkMustClient(
                $this->userPermissionEnum->COMPANY_DISTRIBUTION
            );
            $vender = $user['userId'];
            $distributionId = $this->input->post('distributionId');
            return $this->distributionAo->recommend($vender,$distributionId);
        }
    }

    /**
     * @view json
     * 获取商家信息
     */
    public function getBusinessInfo(){
        if($this->input->is_ajax_request()){
            //检查参数
            $data = $this->argv->checkGet(array(
                array('userId', 'require'),
            ));
            $userId = $data['userId'];
            $client = $this->clientLoginAo->checkMustLogin($userId);
            $clientId = $client['clientId'];
            return $this->distributionAo->getBusinessInfo($userId,$clientId);
        }
    }


    //获取分成
    public function getDistribution(){
        $vender = 10007;
        $userId = 10143;
        $links  = $this->distributionAo->getLinks($vender,$userId);
        echo '<pre>';
        print_r($links);
    }

    //测试推送客服消息
    public function testCustom(){
        echo $_SERVER["HTTP_HOST"];
        // $userId = 10081;
        // $this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
        // $weixinSubscribe = $this->wxSubscribeAo->search($userId,array('remark'=>'新朋友'),'')['data'][0];
        // $weixinSubscribeId = $weixinSubscribe['weixinSubscribeId'];
        // $graphic=$this->wxSubscribeAo->graphicSearch($userId,$weixinSubscribeId);
        // // var_dump($graphic);die;
        // $this->load->model('user/userAppAo','userAppAo');
        // $info   = $this->userAppAo->getTokenAndTicket($userId);
        // $access_token = $info['appAccessToken'];
        // $url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
        // $arr['touser'] = 'oTKRMuJrvqmx0jB2AV2I1JlT35RI';
        // $arr['msgtype']= 'news';
        // $data['title'] = urlencode($graphic[0]['Title']);
        // $data['description'] = urlencode($graphic[0]['Description']);
        // $data['url']   = 'http://10081.shop.tongyinyang.com/10081/distribution/center.html';
        // $data['picurl']= 'http://shop.tongyinyang.com'.$graphic[0]['PicUrl'];
        // $arr['news']['articles'][] = $data;
        // $this->load->library('http');
        // $httpResponse = $this->http->ajax(array(
        //     'url'=>$url,
        //     'type'=>'post',
        //     'data'=>urldecode(json_encode($arr)),
        //     'dataType'=>'plain',
        //     'responseType'=>'json'
        // ));
        // var_dump($httpResponse);die;
    }
}
