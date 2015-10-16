<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class WithDrawAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('withdraw/withDrawDb','withDrawDb');
        $this->load->model('client/clientAo','clientAo');
        $this->load->model('withdraw/moneyLogDb','moneyLogDb');
    }

    public function getFixedPrice($price){
        return sprintf("%.2f", $price);
    }

    //查看提现申请
    public function search($vender,$dataWhere,$dataLimit){
        $dataWhere['vender'] = $vender;
        $result = $this->withDrawDb->search($dataWhere,$dataLimit);
        $data = $result['data'];
        foreach ($data as $key => $value) {
            $data[$key]['money'] = sprintf('%.2f',$value['money']/100);
        }
        $result['data'] = $data;
        return $result;
    }

    //获取详情
    private function getDrawInfo($vender,$withDrawId){
        $info = $this->withDrawDb->getDrawInfo($withDrawId);
        if($info){
            $info = $info[0];
            if($info['vender'] != $vender){
                throw new CI_MyException(1,'无效查看');
            }else{
                return $info;
            }
        }else{
            throw new CI_MyException(1,'无效提现申请id');
        }
    }

    //受理
    public function accept($vender,$withDrawId){
        $info = $this->getDrawInfo($vender,$withDrawId);
        if($info['state'] != 0){
            throw new CI_MyException(1,'提现申请不处于可受理状态');
        }
        $data['state'] = 1;
        $result = $this->withDrawDb->mod($withDrawId,$data);
        if($result){
            return $result;
        }else{
            throw new CI_MyException(1,'受理失败');
        }
    }

    //拒绝
    public function forbid($vender,$withDrawId){
        $info = $this->getDrawInfo($vender,$withDrawId);
        if($info['state'] != 0){
            throw new CI_MyException(1,'提现申请不处于可拒绝状态');
        }
        $data['state'] = 2;
        $result = $this->withDrawDb->mod($withDrawId,$data);
        if($result){
            $clientId = $info['clientId'];
            $clientInfo = $this->clientAo->get($vender,$clientId);
            $fall = $clientInfo['fall'] + $info['money'];
            $data = array();
            $data['fall'] = $fall;
            $result = $this->clientAo->mod($vender,$clientId,$data);
            if($result){
                //写入账户明细
                $data = array();
                $data['vender'] = $vender;
                $data['money']  = $info['money'];
                $data['dis']    = 1;
                $data['clientId'] = $clientId;
                $data['remark'] = '提现申请被拒';
                $data['createTime'] = date('Y-m-d H:i:s',time());
                return $this->moneyLogDb->add($data);
            }else{
                throw new CI_MyException(1,'返还用户余额失败');
            }
        }else{
            throw new CI_MyException(1,'拒绝失败');
        }
    }

    //提现申请
    public function draw($data,$clientId){
        foreach ($data as $key => $value) {
            if($key == 'remark'){
                continue;
            }else{
                if(!$value){
                    throw new CI_MyException(1,'请检测输入参数');
                }else{
                    if($key == 'mobile'){
                        if( preg_match('/^[0-9]{11}$/',$data['mobile']) == 0 )
                            throw new CI_MyException(1,'请输入11位的联系人手机号码');
                    }
                    if($key == 'money'){
                        if($value < 0 || !is_numeric($value)){
                            throw new CI_MyException(1,'金额格式错误');
                        }
                    }
                }
            }
        }
        $clientInfo = $this->clientAo->get($data['userId'],$clientId);
        if($clientInfo['fall'] < $data['money']*100){
            throw new CI_MyException(1,'余额不足');
        }
        $data['vender'] = $data['userId'];
        unset($data['userId']);
        $data['clientId'] = $clientId;
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        $data['money'] = $data['money']*100;
        $money = $data['money'];
        $result = $this->withDrawDb->add($data);
        if($result){
            //提现成功 扣除客户余额
            $vender = $data['vender'];
            $fall = $clientInfo['fall'] - $data['money'];
            $data = array();
            $data['fall'] = $fall;
            $result = $this->clientAo->mod($vender,$clientId,$data);
            if($result){
                //写入账户明细
                $data = array();
                $data['vender'] = $vender;
                $data['money']  = $money;
                $data['dis']    = 0;
                $data['clientId'] = $clientId;
                $data['remark'] = '申请提现';
                $data['createTime'] = date('Y-m-d H:i:s',time());
                return $this->moneyLogDb->add($data);
            }else{
                throw new CI_MyException(1,'扣除余额失败');
            }
        }else{  
            throw new CI_MyException(1,'提现申请失败');
        }
    }

    //获取提现日志
    public function getLog($vender,$clientId){
        $info = $this->withDrawDb->getLog($vender,$clientId);
        foreach ($info as $key => $value) {
            if($value['state'] == 0){
                $info[$key]['showState'] = '未受理';
            }elseif($value['state'] == 1){
                $info[$key]['showState'] = '已受理';
            }elseif($value['state'] == 2){
                $info[$key]['showState'] = '已取消';
            }
            $info[$key]['money'] = sprintf('%.2f',$value['money']/100);
        }
        return $info;
    }

    //获取账户明细
    public function getMoneyLog($vender,$clientId){
        $info = $this->moneyLogDb->getMoneyLog($vender,$clientId);
        foreach ($info as $key => $value) {
            if($value['dis'] == 1){
                $option = '+';
                $info[$key]['type'] = '收入';
            }else{
                $option = '-';
                $info[$key]['type'] = '支出';
            }
            $info[$key]['showMoney'] = $option.sprintf('%.2f',$value['money']/100);
        }
        return $info;
    }

    //获取需要处理的提现申请数量
    public function getNeedHandleNum($vender,$state){
        return $this->withDrawDb->getNeedHandleNum($vender,$state);
    }
}