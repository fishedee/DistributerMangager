<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class dishTypeAo extends CI_Model{
    public function __construct(){
        parent::__construct();
        $this->load->model('dish/dishTypeDb','dishTypeDb');
    }

    //查看菜品分类
    public function search($userId,$dataWhere,$dataLimit){
        $dataWhere['userId'] = $userId;
        $info = $this->dishTypeDb->search($dataWhere,$dataLimit);
        $infoData = $info['data'];
        foreach ($infoData as $key => $value) {
            if($value['parent_id'] == 0){
                $infoData[$key]['parent_name'] = '顶级分类';
            }else{
                $dishTypeInfo = $this->getDetail($userId,$value['parent_id']);
                $infoData[$key]['parent_name'] = $dishTypeInfo['title'];
            }
        }
        if($infoData){
            $infoData = $this->getMenuTree($infoData);
        }
        $info['data'] = $infoData;
        return $info;
    }

    function getMenuTree($arrCat, $parent_id = 0, $level = 0){
        static  $arrTree = array(); //使用static代替global
        if( empty($arrCat)) return FALSE;
        $level++;
        foreach($arrCat as $key => $value)
        {
            if($value['parent_id' ] == $parent_id)
            {
                $value[ 'level'] = $level;
                $arrTree[] = $value;
                unset($arrCat[$key]); //注销当前节点数据，减少已无用的遍历
                $this->getMenuTree($arrCat, $value['dishTypeId'], $level);
            }
        }
       
        return $arrTree;
    }

    //重新组合分类
    public function recursion($parent_id,$arr){
        //判断有无子类
        $childs = $this->getChild($parent_id);
        if(!($n = count($childs))){
            return;
        }
        $cnt = 1;
        for ($i=0; $i < $n; $i++) {
            $arr[] = $childs[$i];
            $this->recursion($childs[$i]['parent_id'],$arr);
            // $cnt++;
        }
        return $arr;
    }

    //判断有无子类
    public function getChild($parent_id){
        return $this->dishTypeDb->getChild($parent_id);
    }


    //选择上级分类
    public function getAllType($userId,$dishTypeId){
        $info = $this->dishTypeDb->getAllType($userId,$dishTypeId);
        $arr  = array();
        $arr[0] = '顶级分类';
        foreach ($info as $key => $value) {
            $arr[$value['dishTypeId']] = $value['title'];
        }
        return $arr;
    }

    //获取详细信息
    public function getDetail($userId,$dishTypeId){
        $info = $this->dishTypeDb->getDetail($dishTypeId);
        if($info['userId'] != $userId){
            throw new CI_MyException(1,'您无权查看');
        }
        return $info;
    }

    //增加菜品分类
    public function addType($userId,$data){
        if(!$data){
            throw new CI_MyException(1,'分类名不能为空');
        }
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        return $this->dishTypeDb->addType($userId,$data);
    }

    //修改菜品分类
    public function modType($userId,$dishTypeId,$data){
        if(!$data){
           throw new CI_MyException(1,'分类名不能为空'); 
        }
        $info = $this->getDetail($userId,$dishTypeId);
        $data['modifyTime'] = date('Y-m-d H:i:s',time());
        return $this->dishTypeDb->modType($dishTypeId,$data);
    }

    //删除分类
    public function del($userId,$dishTypeId){
        $this->getDetail($userId,$dishTypeId);
        return $this->dishTypeDb->del($userId,$dishTypeId);
    }

    //获取分类信息
    public function getTypeInfo($userId){
        $typeInfo = $this->search($userId,array(),array());
        $typeInfo = $typeInfo['data'];
        $arr = array();
        foreach ($typeInfo as $key => $value) {
            $arr[$value['dishTypeId']] = $value['title'];
        }
        return $arr;
    }

    //获取第一个分类
    public function getFirstType($userId){
        return $this->dishTypeDb->getFirstType($userId);
    }

    //前台获取菜单分类
    public function getMenuType($userId){
        return $this->dishTypeDb->getMenuType($userId);
    }
}
