<?php 
/**
 * author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsDb extends CI_Model{
		private $tableName = 't_chips';

		public function __construct(){
			parent::__construct();
			// $this->load->database($this->tableName);
		}

		public function getChips($userId,$mobile,$limit){
			if($userId == 0){
				$chipsInfo = $this->db->get($this->tableName)->result_array();
				return array(
					'count' => count($chipsInfo),
					'data'  => $chipsInfo
					);
			}
			//获取众筹商品
			$condition['userId'] = $userId;
			$condition['is_delete'] = 0;
			if($mobile == 1){
				$condition['status'] = 1;
			}
			$chipsInfo = $this->db->where($condition)->from($this->tableName)->order_by('start DESC')->get()->result_array();
			$count = count($chipsInfo);
			if($limit){
				if( isset($limit["pageIndex"]) && isset($limit["pageSize"])){
					$this->db->limit($limit["pageSize"],$limit["pageIndex"]);
				}
			}
			$chipsInfo = $this->db->where($condition)->from($this->tableName)->order_by('start DESC')->get()->result_array();
			return array(
				'count' => $count,
				'data'  => $chipsInfo
				);
		}

		//用户判断开始状态的方法
		public function judgeStart($start_time,$end_time){
			$now_time = strtotime(date('Y-m-d',time()));
			$start_time = strtotime($start_time);
			$end_time   = strtotime($end_time);
			if($now_time < $start_time){
				$start = 3;
			}elseif($now_time >= $end_time){
				$start = 0;
			}elseif($now_time < $end_time){
				$start = 1;
			}
			return $start;
		}

		//用于判断时间的合理性
		public function judgeTime($start_time,$end_time){
			if(strtotime(date('Y-m-d',time())) > strtotime($start_time) && strtotime(date('Y-m-d',time())) > strtotime($end_time)){
				return false;
			}else{
				return true;
			}
		}

		//处理发布众筹商品
		public function addChips($data){
			$this->db->insert($this->tableName,$data);
			return $this->db->insert_id();
		}

		//检测是否符合删除条件
		public function delInfo($chips_id){
			$chipsInfo = $this->db->select('is_delete,userId')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			return $chipsInfo[0];
		}

		//删除众筹商品
		public function del($chips_id){
			$data['is_delete'] = 1;
			$this->db->where('chips_id',$chips_id)->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//众筹商品的详细信息
		public function chipsDetail($chips_id){
			$condition['chips_id'] = $chips_id;
			$condition['is_delete']= 0;
			$info = $this->db->from($this->tableName)->where($condition)->get()->result_array();
			return $info[0];
		}

		//更新众筹商品
		public function updateChips($chips_id,$data){
			$this->db->where('chips_id',$chips_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//判断更新状态 为3 即还没开始的时候才能更新 或活动已经结束
		public function editStartStatus($chips_id){
			$chipsInfo = $this->db->select('start')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$start = $chipsInfo[0]['start'];
			if($start == 3 || $start == 0){
				return true;
			}else{
				return false;
			}
		}

		//众筹商品的时间状态
		public function changeStart($start_time,$end_time,$chips_id,$start){
			date_default_timezone_set('PRC');
			$now_time = strtotime(date('Y-m-d',time())); // 目前时间
			$start    = strtotime($start_time);          // 开始时间
			$end      = strtotime($end_time);            // 结束时间
			$time_arr['now_time'] = $now_time;
			$time_arr['start']    = $start;
			$time_arr['end']      = $end;
			if($now_time > $end){  // 目前时间已经大于结束时间
				if($start != 0){
					$data['start'] = 0;
					$this->db->where('chips_id',$chips_id);
					$this->db->update($this->tableName,$data);
				}
			}elseif($now_time >= $start && $now_time < $end){  // 目前时间 大于开始时间 小于结束时间
				if($start != 1){
					$data['start'] = 1;
					$this->db->where('chips_id',$chips_id);
					$this->db->update($this->tableName,$data);
				}
			}elseif($now_time < $start && $now_time < $end){  // 目前时间小于开始时间 小于结束时间
				if($start != 3){
					$data['start'] = 3;
					$this->db->where('chips_id',$chips_id);
					$this->db->update($this->tableName,$data);
				}
			}
		}

		

		//更改上下架情况
		public function upOrDown($chips_id){
			$statusInfo = $this->db->select('status')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$status = $statusInfo[0]['status'];
			if($status == 0){
				$data['status'] = 1;
			}else{
				$data['status'] = 0;
			}
			$this->db->where('chips_id',$chips_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//检测订购商品的上下架情况
		public function chipsStatus($chips_id){
			$status = $this->db->select('status')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$status = $status[0]['status'];
			if($status == 1){
				return true;
			}else{
				return false;
			}
		}

		//检测众筹密码
		public function checkPassword($chips_id,$password){
			$passwordInfo = $this->db->select('password')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$pwd = $passwordInfo[0]['password'];
			if($pwd === $password){
				return true;
			}else{
				return false;
			}
		}

		//检测订购数量
		public function checkOrderNum($order_num,$chips_id){
			$order_num_info = $this->db->select('stock')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$stock = $order_num_info[0]['stock'];
			if($order_num > $stock){
				return false;
			}else{
				return true;
			}
		}

		//检测活动状态
		public function chipsStart($chips_id){
			$start = $this->db->select('start')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$start = $start[0]['start'];
			if($start != 1){
				return false;
			}else{
				return true;
			}
		}

		//更新库存
		public function chipsNum($chips_id,$num){
			$oldnum = $this->db->select('stock')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$newnum    = $oldnum[0]['stock'] - $num;
			$data['stock'] = $newnum;
			$this->db->where('chips_id',$chips_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//库存量回滚
		public function rollNum($chips_id,$num){
			$oldnum = $this->db->select('stock')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$newnum    = $oldnum[0]['stock'] + $num;
			$data['stock'] = $newnum;
			$this->db->where('chips_id',$chips_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//支付完 扣除数量 根据后台设定值 更改目前价格
		public function payFirstAfter($chips_id,$num){
			$chipsInfo = $this->db->select('down_num,down_price,num,oldprice,base')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$down_num = $chipsInfo[0]['down_num'];
			$down_price = $chipsInfo[0]['down_price'];
			$now_num = $chipsInfo[0]['num'];
			$oldprice = $chipsInfo[0]['oldprice'];
			$base     = $chipsInfo[0]['base'];
			//目前订购数量
			$now_num = $now_num + $num;
			$dis_price = floor(($now_num/$down_num)) * $down_price;

			$data['num'] = $now_num;
			$data['newprice'] = $oldprice - $dis_price;
			if($data['newprice'] <= $base){
				$data['newprice'] = $base;
			}
			$this->db->where('chips_id',$chips_id);
			$this->db->update($this->tableName,$data);
			return $this->db->affected_rows();
		}

		//获取start
		public function getStart($chips_id){
			$startInfo = $this->db->select('start')->from($this->tableName)->where('chips_id',$chips_id)->get()->result_array();
			$start = $startInfo[0]['start'];
			return $start;
		}
	}
 ?>