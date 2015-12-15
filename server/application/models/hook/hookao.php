<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class HookAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('hook/hookDb','hookDb');
        $this->load->model('user/userAo','userAo');
    }

    public function search($userId,$dataWhere,$dataLimit){
        $dataWhere['userId'] = $userId;
        return $this->hookDb->search($dataWhere,$dataLimit);
    }

    //增加
    public function add($userId,$data){
        if(!$data['hookName']){
            throw new CI_MyException(1,'请输入插件名称');
        }
        $option = array();
        //检测二级菜单
        foreach ($data['hookOptionName'] as $key => $value) {
            if($value == '' || $data['hookOptionUrl'][$key] == ''){
            }else{
                $arr = array();
                $arr['name'] = $value;
                $arr['url']  = $data['hookOptionUrl'][$key];
                $option[] = $arr;
            }
        }
        $data['hookOption'] = json_encode($option);
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        unset($data['hookOptionName']);
        unset($data['hookOptionUrl']);
        return $this->hookDb->add($data);
    }

    //修改
    public function mod($hookId,$data){
        if(!$data['hookName']){
            throw new CI_MyException(1,'请输入插件名称');
        }
        $option = array();
        //检测二级菜单
        foreach ($data['hookOptionName'] as $key => $value) {
            if($value == '' || $data['hookOptionUrl'][$key] == ''){
            }else{
                $arr = array();
                $arr['name'] = $value;
                $arr['url']  = $data['hookOptionUrl'][$key];
                $option[] = $arr;
            }
        }
        $data['hookOption'] = json_encode($option);
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        unset($data['hookOptionName']);
        unset($data['hookOptionUrl']);
        return $this->hookDb->mod($hookId,$data);
    }

    //获取插件详情
    public function getHook($hookId){
        $info = $this->hookDb->getHook($hookId);
        if(!$info){
            throw new CI_MyException(1,'插件Id无效');
        }
        $info = $info[0];
        $option = json_decode($info['hookOption']);
        $hookOptionName = array();
        $hookOptionUrl  = array();
        foreach ($option as $key => $value) {
            $hookOptionName[] = $value->name;
            $hookOptionUrl[]  = $value->url;
        }
        $info['hookOptionName'] = $hookOptionName;
        $info['hookOptionUrl']  = $hookOptionUrl;
        return $info;
    }

    //查询
    public function power($dataWhere,$dataLimit,$hookId){
        $powerInfo = $this->userAo->search($dataWhere,$dataLimit);
        $userInfo  = $powerInfo['data'];
        foreach ($userInfo as $key => $value) {
            $result = $this->checkPower($hookId,$value['userId']);
            if($result){
                $userInfo[$key]['check'] = "<input type='checkbox' userId='".$value['userId']."' name='power[]' checked='true'/>";
            }else{
                $userInfo[$key]['check'] = "<input type='checkbox' userId='".$value['userId']."' name='power[]'/>";
            }
        }
        $powerInfo['data'] = $userInfo;
        return $powerInfo;
    }

    public function changePower($userId,$hookId,$status){
        $data = array();
        //加入权限
        $hookInfo = $this->getHook($hookId);
        $hookPower = $hookInfo['hookPower'];
        if($status){
            if($hookPower){
                $arr = explode(',', $hookPower);
                $arr[] = $userId;
                $data['hookPower'] = implode(',', $arr);
            }else{
                $arr[] = $userId;
                $data['hookPower'] = implode(',', $arr);
            }
        }else{
            //移出权限
            $arr = explode(',', $hookPower);
            foreach ($arr as $key => $value) {
                if($value == $userId){
                    unset($arr[$key]);
                }
            }
            $data['hookPower'] = implode(',', $arr);
        }
        return $this->hookDb->mod($hookId,$data);
    }

    //检测权限
    public function checkPower($hookId,$userId){
        $result = $this->hookDb->checkPower($hookId,$userId);
        return $result;
    }

    //获取权限列表
    public function getUserHook($userId){
        $info = $this->hookDb->getUserHook($userId);
        return $info;
    }
}
