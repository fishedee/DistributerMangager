<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DistributionDb extends CI_Model
{
    var $tableName = 't_distribution';

    public function __construct(){
        parent::__construct();
    }

    public function add($data){
        $this->db->insert($this->tableName, $data);
        return $this->db->insert_id();
    }

    public function mod($distributionId, $data){
        $this->db->where('distributionId', $distributionId);
        $this->db->update($this->tableName, $data);
        return $this->db->affected_rows();
    }

    public function del($distributionId){
        $this->db->where('distributionId', $distributionId);
        $this->db->delete($this->tableName);
    }

    public function get($distributionId){
        $this->db->where('distributionId', $distributionId);
        $query = $this->db->get($this->tableName)->result_array();
        if(count($query) == 0)
            throw new CI_MyException(1, "不存在此条关系");
        else
            return $query[0];
    }

    public function getDownUser($userId){
        $this->db->where('upUserId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
    }

    public function getUpUser($userId){
        $this->db->where('downUserId', $userId);
        $query = $this->db->get($this->tableName)->result_array();
        return $query;
    }

    public function search($where, $limit,$userId=0,$vender=0){
        // $this->db->where($where);
        $result = $this->db->get($this->tableName)->result_array();
        if( isset($where['upUserId']) && count($where['upUserId']) == 0)
            return array(
                'count'=>0,
                'data'=>array()
            );
        if( isset($where['downUserId']) && count($where['downUserId']) == 0)
            return array(
                'count'=>0,
                'data'=>array()
            );
        if($vender && isset($where['upUserId'])){
            // var_dump($userId);die;
            $this->db->where('vender',$userId);
            $query = $this->db->get($this->tableName)->result_array();
            $count = count($query);
            if(isset($limit['pageIndex']) && isset($limit['pageSize']))
                $this->db->limit($limit['pageSize'], $limit['pageIndex']);
            foreach ($where as $key => $value) {
            	if($key == 'state')
                    $this->db->where($key, $value);
            }
            $this->db->order_by('scort','asc');
            $this->db->where('vender',$userId);
            $query = $this->db->get($this->tableName)->result_array();
            // if($query){
            //     $query = $this->getMenuTree($query);
            // }
            return array(
                'count'=>$count,
                'data'=>$query
            );
        }else{
            foreach($where as $key=>$value){
                if($key == 'upUserId' || $key == 'downUserId')
                    $this->db->where($key, $value);
                else if($key == 'state')
                    $this->db->where($key, $value);
            }
            $this->db->order_by('createTime', 'desc');
            if(isset($limit['pageIndex']) && isset($limit['pageSize']))
                $this->db->limit($limit['pageSize'], $limit['pageIndex']);
            $query = $this->db->get($this->tableName)->result_array();
            return array(
                'count'=>count($query),
                'data'=>$query
            );
        }
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
                $this->getMenuTree($arrCat, $value['distributionId'], $level);
            }
        }
       
        return $arrTree;
    }

    public function distributionSearch($data,$arr = array()){
        $count = 0;
        $sum = count($arr) ? count($arr) : count($data);
        foreach ($data as $key => $value) {
            $arr[] = $data[$key];
            $count++;
            $this->db->where('upUserId',$value['downUserId']);
            $result = $this->db->get($this->tableName)->result_array();
            if($result){
                foreach ($result as $key => $value) {
                    $arr[] = $value;
                }
            }
            if($sum == $count){
                $this->distributionSearch($result,$arr);
            }
        }
        return $arr;
    }

    //判断有无上级
    public function judgeUp($upUserId){
        $this->db->where('downUserId',$upUserId);
        $this->db->select('upUserId');
        $result = $this->db->get($this->tableName)->result_array();
        if($result){
            //循环查找
            while (1) {
                $id = $result[0]['upUserId'];
                $this->db->where('downUserId',$id);
                $this->db->select('upUserId');
                $result = $this->db->get($this->tableName)->result_array();
                if($result){
                    continue;
                }else{
                    break;
                }
            }
            return $id;
        }else{
            return $upUserId;
        }
    }

    //查找最大的line值
    public function checkLine($vender){
        $sql = "SELECT MAX(line) from t_distribution WHERE vender={$vender}";
        return $this->db->query($sql)->result_array();
    }

    //获取上线信息
    public function getUp($vender,$upUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$upUserId);
        $this->db->select('distributionId');
        $this->db->select('line');
        $this->db->select('scort');
        return $this->db->get($this->tableName)->result_array();
    }

    //判断有无上线
    public function checkUp($vender,$downUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$downUserId);
        $this->db->select('distributionId');
        $this->db->select('upUserId');
        return $this->db->get($this->tableName)->result_array();
    }

    //判断有无建立分成关系
    public function checkHasDistribution($vender,$downUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$downUserId);
        $this->db->where('state',2);
        $this->db->select('distributionId');
        return $this->db->get($this->tableName)->result_array();
    }

    //获取分成关系
    public function getDistribution($distributionId){
        $this->db->where('distributionId',$distributionId);
        return $this->db->get($this->tableName)->result_array();
    }

    //我的盟友
    public function myAllies($vender,$myUserId){
        $this->db->where('vender',$vender);
        $this->db->where('upUserId',$myUserId);
        //一级盟友
        $info = $this->db->get($this->tableName)->result_array();
        $first  = count($info);
        //二级盟友
        $second = 0;
        foreach ($info as $key => $value) {
            $this->db->where('vender',$vender);
            $this->db->where('upUserId',$value['downUserId']);
            $result = $this->db->get($this->tableName)->result_array();
            $second += count($result);
        }
        return array(
            'first'=>$first,
            'second'=>$second
            );
    }

    //获取我的盟友信息
    public function getAllies($vender,$myUserId,$allies){
        $this->db->where('vender',$vender);
        $this->db->where('upUserId',$myUserId);
        $info = $this->db->get($this->tableName)->result_array();
        if($allies == 1){
            //一级盟友
            return array(
                'count'=>count($info),
                'data' =>$info
                );
        }else{
            //二级盟友
            $two = array();
            foreach ($info as $key => $value) {
                $this->db->where('vender',$vender);
                $this->db->where('upUserId',$value['downUserId']);
                $result = $this->db->get($this->tableName)->result_array();
                foreach ($result as $key => $value) {
                    $two[] = $value;
                }
            }
            return array(
                'count'=>count($two),
                'data' =>$two
                );
        }
    }

    public function getTopAllies($vender,$allies){
        $this->db->where('vender',$vender);
        $this->db->where('scort',1);
        $info = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>count($info),
            'data' =>$info
            );
    }

    //获取第几级分销
    public function getScort($vender,$userId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$userId);
        return $this->db->get($this->tableName)->result_array();
    }

    //获取一级代理商
    public function getOneScort($vender,$line){
        $this->db->where('line',$line);
        $this->db->where('vender',$vender);
        $this->db->where('scort',1);
        return $this->db->get($this->tableName)->result_array();
    }

    //检测我的等级
    public function checkMyDegree($vender,$myUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$myUserId);
        return $this->db->get($this->tableName)->result_array();
    }

    //获取最大跟最小等级
    public function getMaxAndMin($vender,$line){
        $sql = "SELECT MAX(scort),MIN(scort) FROM t_distribution WHERE vender={$vender} AND line={$line}";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    //获取一下的所有下线
    public function getAfter($vender,$scort,$line){
        $sql = "SELECT * FROM t_distribution WHERE vender={$vender} AND line={$line} AND scort>={$scort} ORDER BY distributionId ASC";
        $result = $this->db->query($sql)->result_array();
        return $result;
    }

    //获取推荐总代信息
    public function getRecommend($userId){
        $this->db->where('vender',$userId);
        $this->db->where('recommend',1);
        $this->db->order_by('distributionId','desc');
        return $this->db->get($this->tableName)->result_array();
    }

    //获取下线有多少级
    public function getMaxScort($vender){
        $sql = "SELECT MAX(scort) FROM t_distribution WHERE vender={$vender}";
        return $this->db->query($sql)->result_array();
    }

    //获取该级别有多少人
    public function getScortNum($vender,$scort){
        $this->db->where('vender',$vender);
        $this->db->where('scort',$scort);
        return $this->db->get($this->tableName)->num_rows();
    }

    /**
     * 查询会员
     * date:2015.11.27
     */
    public function searchMember($where,$limit){
        $this->load->model('distribution/distributionStateEnum','distributionStateEnum');
        foreach ($where as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->where('state',$this->distributionStateEnum->ON_ACCEPT);
        $count = $this->db->get($this->tableName)->num_rows();
        foreach ($where as $key => $value) {
            $this->db->where($key,$value);
        }
        $this->db->where('state',$this->distributionStateEnum->ON_ACCEPT);
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>$count,
            'data' =>$query
            );
    }

    /**
     * 获取用户分成关系
     * date:2015.11.30
     */
    public function getDistributionUser($vender,$downUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$downUserId);
        return $this->db->get($this->tableName)->result_array();
    }

    /**
     * 获取分成id
     * date:2015.12.08
     */
    public function getDistributionId($vender,$downUserId){
        $this->db->where('vender',$vender);
        $this->db->where('downUserId',$downUserId);
        $this->db->select('distributionId');
        return $this->db->get($this->tableName)->row_array();
    }
}
