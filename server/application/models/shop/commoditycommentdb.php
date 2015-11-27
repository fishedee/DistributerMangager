<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class CommodityCommentDb extends CI_Model {

	private $tableName = 't_shop_commodity_comment';

	public function __construct(){
		parent::__construct();
	}

	public function search($where, $limit){
		foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where($key, $value);
        }
        $count = $this->db->count_all_results($this->tableName);
        foreach($where as $key=>$value){
            if($key == 'title' || $key == 'introduction' )
                $this->db->like($key, $value);
            else if($key == 'shopCommodityId')
                $this->db->where($key, $value);
        }
        if(isset($limit['pageIndex']) && isset($limit['pageSize']))
            $this->db->limit($limit['pageSize'], $limit['pageIndex']);
        $query = $this->db->get($this->tableName)->result_array();
        return array(
            'count'=>$count,
            'data'=>$query
        );
	}

    public function getComment($shopCommodityId){
        $sql = "SELECT a.*,b.nickName,b.headImgUrl FROM t_shop_commodity_comment AS a INNER JOIN t_client AS b ON a.clientId = b.clientId WHERE a.shopCommodityId={$shopCommodityId} ORDER BY a.createTime DESC";
        return $this->db->query($sql)->result_array();
    }

    public function add($data){
        $data['createTime'] = date('Y-m-d H:i:s',time());
        $this->db->insert($this->tableName,$data);
        return $this->db->insert_id();
    }

    //获取评论详情
    public function getCommentDetail($commentId){
        $this->db->where('commentId',$commentId);
        return $this->db->get($this->tableName)->result_array();
    }

    //删除评价
    public function del($commentId){
        $this->db->where('commentId',$commentId);
        $this->db->delete($this->tableName);
        return $this->db->affected_rows();
    }
}
