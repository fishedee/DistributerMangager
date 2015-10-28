<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dishAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('dish/dishTypeAo','dishTypeAo');
        $this->load->model('dish/dishDb','dishDb');
        $this->load->model('dish/dishCommentDb','dishCommentDb');
        $this->load->model('client/clientAo','clientAo');
    }

    //查看菜品
    public function search($userId,$dataWhere,$dataLimit){
        $dataWhere['userId'] = $userId;
        $info = $this->dishDb->search($dataWhere,$dataLimit);
        foreach ($info['data'] as $key => $value) {
            $info['data'][$key]['dishPrice'] = sprintf("%.2f", $info['data'][$key]['dishPrice']/100);
        }
        return $info;
    }

    //增加菜品
    public function add($userId,$data){
        //处理参数
        if(!$data['dishName']){
            throw new CI_MyException(1,'菜品名不能为空'); 
        }
        if(!$data['dishPrice'] || !is_numeric($data['dishPrice']) || $data['dishPrice'] < 0){
            throw new CI_MyException(1,'菜品价格非法');
        }
        if(!$data['icon']){
            throw new CI_MyException(1,'请上传菜品主图');
        }
        if(isset($data['dishOption'])){
            foreach ($data['dishOption'] as $key => $value) {
                if(!$value){
                    unset($data['dishOption'][$key]);
                }
            }
        }

        //生成缩略图
        $icon = dirname(__FILE__).'/../../../..'.$data['icon'];
        $this->load->library('image_lib');
        $config['image_library'] = 'gd2';
        $config['source_image'] = $icon;
        $config['create_thumb'] = TRUE;
        $config['maintain_ratio'] = TRUE;
        $config['width']     = 90;
        $config['height']   = 60;
        $this->image_lib->initialize($config);
        $imageInfo  = getimagesize($icon);
        $postfix = 'jpg';
        if($imageInfo[2] == 1){
            $postfix = 'gif';
        }else if($imageInfo[2] == 2){
            $postfix = 'jpg';
        }else{
            $postfix = 'gif';
        }
        if(!$this->image_lib->resize()){
            throw new CI_MyException(1,'生成缩略图失败');
        }
        $thumb_icon = substr($data['icon'], 0,strrpos($data['icon'], '.')).'_thumb.'.$postfix;

        if(isset($data['dishOption'])){
            $data['dishOption'] = implode(',', $data['dishOption']);
        }else{
            $data['dishOption'] = '';
        }
        $data['dishPrice']  = $data['dishPrice'] * 100;
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        $data['thumb_icon'] = $thumb_icon;
        return $this->dishDb->add($userId,$data);
    }

    //编辑菜品信息
    public function mod($userId,$dishId,$data){
        if($this->checkUserId($userId,$dishId)){
            foreach ($data['dishOption'] as $key => $value) {
                if(!$value){
                    unset($data['dishOption'][$key]);
                }
            }
            //生成缩略图
            $icon = dirname(__FILE__).'/../../../..'.$data['icon'];
            $this->load->library('image_lib');
            $config['image_library'] = 'gd2';
            $config['source_image'] = $icon;
            $config['create_thumb'] = TRUE;
            $config['maintain_ratio'] = TRUE;
            $config['width']     = 90;
            $config['height']   = 60;
            $this->image_lib->initialize($config);
            $imageInfo  = getimagesize($icon);
            $postfix = 'jpg';
            if($imageInfo[2] == 1){
                $postfix = 'gif';
            }else if($imageInfo[2] == 2){
                $postfix = 'jpg';
            }else{
                $postfix = 'gif';
            }
            if(!$this->image_lib->resize()){
                throw new CI_MyException(1,'生成缩略图失败');
            }
            $thumb_icon = substr($data['icon'], 0,strrpos($data['icon'], '.')).'_thumb.'.$postfix;
            $data['thumb_icon'] = $thumb_icon;
            $data['dishOption'] = implode(',', $data['dishOption']);
            $data['dishPrice']  = $data['dishPrice'] * 100;
            $data['modifyTime'] = date('Y-m-d H:i:s',time());
            return $this->dishDb->mod($dishId,$data);
        }else{
            throw new CI_MyException(1,'无权编辑');
        }
    }

    //检测userId
    public function checkUserId($userId,$dishId){
        $dishInfo = $this->dishDb->checkUserId($dishId);
        if($dishInfo){
            if($dishInfo[0]['userId'] != $userId){
                return FALSE;
            }else{
                return TRUE;
            }
        }else{
            return FALSE;
        }
    }

    //获取菜品信息
    public function getDish($userId,$dishId,$mobile = '0'){
        $dishInfo = $this->dishDb->getDish($dishId);
        if($dishInfo){
            if($dishInfo[0]['userId'] != $userId){
                throw new CI_MyException(1,'无权编辑');
            }else{
                $dishInfo = $dishInfo[0];
                $dishInfo['dishPrice'] = $dishInfo['dishPrice']/100;
                $dishInfo['dishOption']= explode(',', $dishInfo['dishOption']);
                if($mobile){
                    //查询评论
                    $dataWhere['dishId'] = $dishId;
                    $limit['pageIndex']  = 0;
                    $limit['pageSize']   = 3;
                    $commentInfo = $this->dishCommentDb->search($dataWhere,$limit);
                    $commentInfo = $commentInfo['data'];
                    foreach ($commentInfo as $key => $value) {
                        $clientInfo = $this->clientAo->get($userId,$value['clientId']);
                        $nickName   = $clientInfo['nickName'];
                        $headImgUrl = $clientInfo['headImgUrl'];
                        $commentInfo[$key]['nickName'] = $nickName;
                        $commentInfo[$key]['headImgUrl'] = $headImgUrl;
                    }
                    $dishInfo['comment'] = $commentInfo;
                }
                // $dishInfo['detail'] = htmlspecialchars($dishInfo['detail']);
                return $dishInfo;
            }
        }else{
            throw new CI_MyException(1,'ID无效');
        }
    }

    public function commentGetDish($userId,$dishId){
        $dishInfo = $this->dishDb->commentGetDish($dishId);
        if($dishInfo){
            return $dishInfo[0];
        }else{
            throw new CI_MyException(1,'无效菜品id');
        }
    }

    //首页菜品信息
    public function getIndexDish($userId,$dishId){
        $dishInfo = $this->dishDb->getIndexDish($dishId);
        if($dishInfo){
            if($dishInfo[0]['userId'] != $userId){
                throw new CI_MyException(1,'无权编辑');
            }else{
                $dishInfo = $dishInfo[0];
                $dishInfo['dishPrice'] = $dishInfo['dishPrice']/100;
                $dishInfo['dishOption']= explode(',', $dishInfo['dishOption']);
                return $dishInfo;
            }
        }else{
            throw new CI_MyException(1,'ID无效');
        }
    }

    //更改商品上下架状态
    public function modState($userId,$dishId){
        $dishInfo = $this->getDish($userId,$dishId);
        if($dishInfo['state'] == 1){
            $data['state'] = 0;
        }else{
            $data['state'] = 1;
        }
        return $this->dishDb->modState($dishId,$data);
    }

    //删除菜品
    public function del($userId,$dishId){
        if($this->checkUserId($userId,$dishId)){
            return $this->dishDb->del($dishId);
        }else{
            throw new CI_MyException(1,'无权删除');
        }
    }

    //前端获取菜单
    public function getTypeMenu($userId,$dish_type_id){
        if(!$dish_type_id){
            $dishTypeId = $this->dishTypeAo->getFirstType($userId);
            if(!$dishTypeId){
                return array();
            }
            $dish_type_id = $dishTypeId[0];
        }
        $dishInfo = $this->dishDb->getMenu($userId,$dish_type_id);
        foreach ($dishInfo as $key => $value) {
            $dishInfo[$key]['dishPrice'] = sprintf('%.2f',$dishInfo[$key]['dishPrice']/100);
        }
        return $dishInfo;
    }

    //前端获取菜单
    public function getMenu($userId){
        $dishTypeInfo = $this->dishTypeAo->getMenuType($userId);
        $arr = array();
        $arr2= array();
        foreach ($dishTypeInfo as $key => $value) {
            if($value['parent_id'] == 0){
                $arr[$value['dishTypeId']]['dishTypeId'] = $value['dishTypeId'];
                $arr[$value['dishTypeId']]['title'] = $value['title'];
                $arr[$value['dishTypeId']]['data'] = array();
                $dishInfo = $this->getTypeMenu($userId,$value['dishTypeId']);
                if($dishInfo){
                    // $arr[$value['dishTypeId']]['firstData'][] = $dishInfo;
                    $arr[$value['dishTypeId']]['firstData'] = $dishInfo;
                }
            }else{
                $arr2[]= $value;
            }
        }
        foreach ($arr2 as $key => $value) {
            $dishTypeId = $value['dishTypeId'];
            $dishInfo   = $this->getTypeMenu($userId,$dishTypeId);
            $arr2[$key]['menu'] = $dishInfo;
        }
        foreach ($arr2 as $key => $value) {
            $arr[$value['parent_id']]['data'][] = $value;
        }
        return $arr;
    }
}
