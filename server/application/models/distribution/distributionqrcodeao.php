<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionQrCodeAo extends CI_Model{

    public function __construct(){
        parent::__construct();
        $this->load->model('user/loginAo', 'loginAo');
        $this->load->model('distribution/distributionQrCodeDb','distributionQrCodeDb');
        $this->load->model('client/clientAo','clientAo');
        $this->load->model('user/userAo','userAo');
        $this->load->model('user/userAppAo','userAppAo');
        $this->load->library('http');
        $this->load->model('distribution/distributionQrcodeConfigDb','distributionQrcodeConfigDb');
    }

    //我的二维码
    public function qrCode($userId,$limit){
        return $this->distributionQrCodeDb->qrCode($userId,$limit);
    }

    //生成我的二维码(永久二维码)
    public function createQrCode($userId,$distribution = array()){
        $qr = '';
        //处理上传二维码
        $info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        //创建永久二维码请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        //开始处理上传二维码
        if(!$distribution){
            $arr['action_name'] = 'QR_LIMIT_STR_SCENE';
            $arr['action_info']['scene']['scene_str'] = 'distribution';
            //发送请求
            $httpResponse = $this->http->ajax(array(
                'url'=>$url,
                'type'=>'post',
                'data'=>json_encode($arr),
                'dataType'=>'plain',
                'responseType'=>'json'
            ));
            if($httpResponse['body']['ticket']){
                $ticket = $httpResponse['body']['ticket'];
                $qr = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
            }
            return $this->distributionQrCodeDb->createQrCode($userId,$qr);
        }else{
            $arr['action_name'] = 'QR_LIMIT_STR_SCENE';
            $distributionData[]  = $userId;
            $distributionData[]  = $distribution['downUserId'];
            $distributionData[]  = $distribution['line'];
            $arr['action_info']['scene']['scene_str'] = implode(',', $distributionData);
            // $arr['action_info']['scene']['scene_id'] = implode(',', $distributionData);
            //发送请求
            $httpResponse = $this->http->ajax(array(
                'url'=>$url,
                'type'=>'post',
                'data'=>json_encode($arr),
                'dataType'=>'plain',
                'responseType'=>'json'
            ));
            if($httpResponse['body']['ticket']){
                $ticket = $httpResponse['body']['ticket'];
                $qr = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
            }
            $data['qrcode'] = $qr;
            $data['qrcodeCreateTime'] = time();
            $data['ticket'] = $ticket;
            return $this->userAo->modInfo($distribution['downUserId'],$data);
        }
    }

    //获取二维码
    public function getQrCode($userId){
        $result = $this->distributionQrCodeDb->getQrCode($userId);
        if($result){
            return $result[0]['qrcode'];
        }else{
            throw new CI_MyException(1,'该商家还没有生成二维码');
        }
    }

    //扫描二维码绑定公众号
    public function qrAsk($userId,$clientId){
        $this->load->model('user/userTypeEnum','userTypeEnum');
        $this->load->model('distribution/distributionAo','distributionAo');
        //检测权限
        $client = $this->clientAo->get($userId,$clientId);
        $result = $this->userAo->checkClientId($userId,$clientId);
        if($result){
            //已经绑定
        }else{
            //还没绑定 系统自动分配账号密码
            $userInfo = $this->userAo->get($userId);
            $username = 'weiyd'.$userInfo['name'].$userInfo['distributionNum'];
            $password = $this->randomKeys(8);
            $data['name'] = $username;
            $data['password'] = $password;
            $data['password2']= $password;
            $data['company']  = $client['nickName'].'(系统自动分配)';
            $data['telephone'] = '0000';
            $data['phone'] = '00000000000';
            $data['type']  = $this->userTypeEnum->CLIENT;
            //检查权限
            if($this->session->userdata('clientId')){
                //非登录用户只能添加商城用户
                $data['type'] = $this->userTypeEnum->CLIENT;
                unset($data['permission']);
                //非登陆用户，注册账号有普通商城和普通分销权限
                $data['permission']=array(2,3);
                unset($data['client']);
            }else{
                throw new CI_MyException(1, "请用手机端登陆");
            }
            $result = $this->distributionAo->scanAskReg($userId,$data);
            if($result){
                //更新
                $data = array();
                $data['distributionNum'] = $userInfo['distributionNum'] + 1;
                $openIdInfo = $userInfo['openIdInfo'];
                if($openIdInfo){
                    $arr = explode(',', $openIdInfo);
                    $arr[] = $clientId;
                    $data['openIdInfo'] = implode(',', $arr);
                }else{
                    $data['openIdInfo'] = $clientId;
                }
                $this->userAo->mod($userId,$data);
                $this->autoReply($userId,$clientId,$username,$password);
            }else{
                throw new CI_MyException(1,'申请分销失败');
            }
        }
    }

    public function qrAsk2($userId,$openId){
        $this->load->model('user/userTypeEnum','userTypeEnum');
        $this->load->model('distribution/distributionAo','distributionAo');
        //检测权限
        $clientData['userId'] = $userId;
        $clientData['type']   = 2;
        $clientData['openId'] = $openId;
        $clientId = $this->clientAo->addOnce($clientData);
        $result = $this->userAo->checkClientId($userId,$clientId);
        if($result[0]['userId']){
            //已经绑定
            $content = '系统已经分配账号密码及提交的分成请求';
            return $content;
        }else{
            //还没绑定 系统自动分配账号密码
            $userInfo = $this->userAo->get($userId);
            $username = 'weiyd'.$userInfo['name'].$userInfo['distributionNum'];
            $password = $this->randomKeys(8);
            $data['name'] = $username;
            $data['password'] = $password;
            $data['password2']= $password;
            $data['company']  = $client['nickName'].'(系统自动分配)';
            $data['telephone'] = '0000';
            $data['phone'] = '00000000000';
            $data['type']  = $this->userTypeEnum->CLIENT;
            $data['clientId'] = $clientId;
            //检查权限
            if($clientId){
                //非登录用户只能添加商城用户
                $data['type'] = $this->userTypeEnum->CLIENT;
                unset($data['permission']);
                //非登陆用户，注册账号有普通商城和普通分销权限
                $data['permission']=array(2,3,5);
                unset($data['client']);
            }else{
                throw new CI_MyException(1, "请用手机端登陆");
            }
            $result = $this->distributionAo->scanAskReg($userId,$data);
            if($result){
                //更新
                $data = array();
                $data['distributionNum'] = $userInfo['distributionNum'] + 1;
                $openIdInfo = $userInfo['openIdInfo'];
                if($openIdInfo){
                    $arr = explode(',', $openIdInfo);
                    $arr[] = $clientId;
                    $data['openIdInfo'] = implode(',', $arr);
                }else{
                    $data['openIdInfo'] = $clientId;
                }
                $this->userAo->mod($userId,$data);
                // $this->autoReply($userId,$clientId,$username,$password);
                $content = "感谢您关注肥鸡服务号,您的账号是:".$username.",密码是:".$password.".工作人员会尽快审核您的申请";
                return $content;
            }else{
                throw new CI_MyException(1,'申请分销失败');
            }
        }
    }

    //随机生成字符串
    private function randomKeys($length){
        $outPut = '';
        for ($i=0; $i < $length; $i++) { 
            $outPut .= chr(mt_rand(33,126));
        }
        return $outPut;
    }

    //回复账号密码
    private function autoReply($userId,$clientId,$username,$password){
        $this->load->library('http');
        $clientInfo = $this->clientAo->get($userId,$clientId);
        $userInfo = $this->userAppAo->get($userId);
        $access_token = $userInfo['appAccessToken'];
        $content = "感谢您关注肥鸡服务号,您的账号是:".$username.",密码是:".$password.".工作人员会尽快审核您的申请";
        $url     = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$access_token;
        $arr['touser'] = $clientInfo['openId'];
        $arr['msgtype']= 'text';
        $arr['text']['content'] = urlencode($content);
        $httpResponse = $this->http->ajax(array(
            'url'=>$url,
            'type'=>'post',
            'data'=>urldecode(json_encode($arr)),
            'dataType'=>'plain',
            'responseType'=>'json'
        ));
        return $httpResponse['body']['errcode'];
    }

    /**
     * @view json
     * 创建临时二维码
     * date:2015.12.03
     */
    public function createLimitQrcode($userId,$distribution){
        $qr = '';
        $ticket = '';
        //获取access_token
        $info = $this->userAppAo->getTokenAndTicket($userId);
        $access_token = $info['appAccessToken'];
        //加入分销二维码配置
        $data['vender'] = $userId;
        $data['downUserId'] = $distribution['downUserId'];
        $data['line'] = $distribution['line'];
        $distributionConfigId = $this->distributionQrcodeConfigDb->add($data);
        //处理二维码参数
        $arr['action_name'] = 'QR_SCENE';
        $arr['expire_seconds'] = 2592000;
        $arr['action_info']['scene']['scene_id'] = $distributionConfigId;
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
        //发送请求
        $httpResponse = $this->http->ajax(array(
            'url'=>$url,
            'type'=>'post',
            'data'=>json_encode($arr),
            'dataType'=>'plain',
            'responseType'=>'json'
        ));
        if($httpResponse['body']['ticket']){
            $ticket = $httpResponse['body']['ticket'];
            $qr = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        }
        $data = array();
        $data['qrcode'] = $qr;
        $data['qrcodeCreateTime'] = time();
        $data['qrcodeLimit'] = 1;
        // $data['qrcodeLimitTime']  = $httpResponse['body']['expire_seconds'];
        $data['qrcodeLimitTime']  = 2592000;
        $data['ticket'] = $ticket;
        return $this->userAo->modInfo($distribution['downUserId'],$data);
    }

    /**
     * @view json
     * 获取分销二维码配置
     * date:2015.12.03
     */
    public function getQrcodeConfig($configId){
        return $this->distributionQrcodeConfigDb->getQrcodeConfig($configId);
    }

}
