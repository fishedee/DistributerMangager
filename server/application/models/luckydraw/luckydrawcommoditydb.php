<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class LuckyDrawCommodityDb extends CI_Model 
{
	var $tableName = "t_lucky_draw_commodity";
	var $field = array(
		'luckyDrawId'=>'',
		'title'=>'',
		'image'=>'',
		'type'=>1,
		'quantity'=>'',
		'coupon_id'=>'',
		'precent'=>'',
		'sort'=>''
	);

	public function __construct(){
		parent::__construct();
	}

	public function reduceQuantity($luckyDrawCommodityId,$quantity=1){
		 $sql = 'update '.$this->tableName.' '.
            'set quantity = quantity - '.$quantity.' '.
            'where quantity >= '.$quantity.' and luckyDrawCommodityId = '.$luckyDrawCommodityId;
        $this->db->query($sql);
        if( $this->db->affected_rows() == 0 )
            throw new CI_MyException(1,'扣减奖品库存失败');
	}

	public function getByLuckyDrawId($luckyDrawId){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->order_by('sort','asc');
		return $this->db->get($this->tableName)->result_array();
	}

	public function delByLuckyDrawId( $luckyDrawId ){
		$this->db->where("luckyDrawId",$luckyDrawId);
		$this->db->delete($this->tableName);
	}
	
	public function addBatch( $data ){
		if( count($data) == 0 )
			return;
		foreach( $data as $key=>$singleData ){
			$data[$key] = array_intersect_key($data[$key],$this->field);
		}
		$this->db->insert_batch($this->tableName,$data);
	}

}
