<?php 
/**
 * @author:zzh
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class ChipsAo extends CI_Model{
		private $tableName = 't_chips';

		public function __construct(){
			parent::__construct();
			$this->load->model('chips/chipsDb','chipsDb');
			$this->load->model('chips/chipsRecordDb','record');
			$this->load->model('chips/chipsPowerDb','power');
		}

		//获取众筹商品信息
		public function getChips($userId,$mobile,$limit){
			return $this->chipsDb->getChips($userId,$mobile,$limit);
		}

		//判断众筹商品的状态
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

		//判断时间的合理性
		public function judgeTime($start_time,$end_time){
			if(strtotime(date('Y-m-d',time())) > strtotime($start_time) && strtotime(date('Y-m-d',time())) > strtotime($end_time)){
				return false;
			}else{
				return true;
			}
		}

		//增加众筹商品
		public function addChips($data){
			//执行下面的操作的时候 首先要验证权限 如是否登陆 跟是否有发布的权限
			if($this->session->userdata('userId')){
				$result = $this->usermodel->checkPermission($this->session->userdata('userId'));
				if(!$result){
					throw new CI_MyException(1,"您没有权限");
				}
			}

			$userId = $this->session->userdata('userId');
			$data['down_num'] = (int)$data['down_num'];
			if($data['product_title'] == ''){
				throw new CI_MyException(1,"商品标题不能为空");
			}
			if($this->chipsmodel->judgeTime($data['start_time'],$data['end_time']) == false){
				throw new CI_MyException(1,"时间格式错误");
			}
			//检测oldprice percent down_num down_price base
			if(!is_numeric($data['oldprice']) || $data['oldprice'] <= 0){
				throw new CI_MyException(1,"初始化价格错误");
			}
			if(!is_numeric($data['percent']) || $data['percent'] <= 0){
				throw new CI_MyException(1,"降价百分比错误");
			}
			if(!is_numeric($data['down_num']) || $data['down_num'] <= 0 || !(is_int($data['down_num']))){
				throw new CI_MyException(1,"降价数量错误");
			}
			if(!is_numeric($data['down_price']) || $data['down_price'] <= 0){
				throw new CI_MyException(1,"降价价格错误");
			}
			if(!is_numeric($data['base']) || $data['base'] <= 0){
				throw new CI_MyException(1,"底价错误");
			}

			$total = $data['base'] * $data['percent'] * 0.01;
			if($total < 0.005){
				throw new CI_MyException(1,'底价过低');
			}

			$data['userId'] = $userId;
			$data['create_time'] = date('Y-m-d',time());
			// $data['showpassword']= $data['password'];
			$data['password'] = md5($data['password']);

			$start_time = $data['start_time'];
			$end_time   = $data['end_time'];
			if($start_time > $end_time){
				$data['start_time'] = $end_time;
				$data['end_time']   = $start_time;
			}else if($start_time == $end_time){
				$data['end_time'] = $end_time + 86400 - 1;
			}
			$start_time = strtotime($data['start_time']);
			$end_time   = strtotime($data['end_time']);
			$data['start'] = $this->judgeStart($start_time,$end_time);
			$data['newprice'] = $data['oldprice'];
			return $this->chipsDb->addChips($data);
		}

		//删除众筹商品
		public function del($chips_id){
			$userId = $userId = $this->session->userdata('userId');

			//检测是否符合删除的资格
			$delInfo = $this->chipsDb->delInfo($chips_id);
			if($delInfo['is_delete'] == 1 || $delInfo['userId'] != $userId){
				throw new CI_MyException(1,'不满足删除的条件');
			}
			return $this->chipsDb->del($chips_id);
		}

		//获取众筹商品的详细信息
		public function chipsDetail($chips_id,$userId){
			$chipsDetailInfo = $this->chipsDb->chipsDetail($chips_id);
			//查询人数
			$person = $this->record->checkPersonNum($chips_id);
			// var_dump($person);die;
			$chipsDetailInfo['person'] = $person;
			// var_dump($chipsDetailInfo);die;
			if($chipsDetailInfo['userId'] != $userId){
				throw new CI_MyException(1,'非发布者');
			}
			if(empty($chipsDetailInfo)){
				throw new CI_MyException(1,'查询错误');
			}else{
				$chipsDetailInfo['mobilePassword'] = $chipsDetailInfo['password'];
				// $chipsDetailInfo['password'] = $chipsDetailInfo['showpassword'];
				return $chipsDetailInfo;
			}
		}

		//更新众筹商品
		public function updateChips($chips_id,$data){

			$userId = $this->chipsDb->delInfo($chips_id)['userId'];
			$data['down_num'] = (int)$data['down_num'];
			if($userId = $this->session->userdata('userId') != $userId){
				throw new CI_MyException(1,'非发布者');
			}

			if($data['product_title'] == ''){
				throw new CI_MyException(1,'标题不能为空');
			}
			if($this->chipsmodel->judgeTime($data['start_time'],$data['end_time']) == false){
				throw new CI_MyException(1,'时间格式错误');
			}
			//检测oldprice percent down_num down_price base
			if(!is_numeric($data['oldprice']) || $data['oldprice'] <= 0){
				throw new CI_MyException(1,'出事价格错误');
			}
			if(!is_numeric($data['percent']) || $data['percent'] <= 0){
				throw new CI_MyException(1,'降价百分比错误');
			}
			if(!is_numeric($data['down_num']) || $data['down_num'] <= 0 || !(is_int($data['down_num']) )){
				throw new CI_MyException(1,'降价数量错误');
			}
			if(!is_numeric($data['down_price']) || $data['down_price'] <= 0){
				throw new CI_MyException(1,'降价价格错误');
			}
			if(!is_numeric($data['base']) || $data['base'] <= 0){
				throw new CI_MyException(1,'底价错误');
			}

			$total = $data['base'] * $data['percent'] * 0.01;
			if($total < 0.005){
				throw new CI_MyException(1,'底价过低');
			}
			$data['start'] = $this->judgeStart($data['start_time'],$data['end_time']);
			// $data['showpassword'] = $data['password'];
			$data['password'] = md5($data['password']);
			return $this->chipsDb->updateChips($chips_id,$data);
		}

		//上下架处理
		public function upOrDown($chips_id){
			return $this->chipsDb->upOrDown($chips_id);
		}

		//检测密码
		public function checkPassword($chips_id,$password){
			$result = $this->chipsDb->checkPassword($chips_id,$password);
			if($result == true){
				$this->power->changePower($this->session->userdata('clientId'),$chips_id,1);
				return $password;
			}else{
				return 0;
			}
		}

		//支付完 扣除数量 根据后台设定值 更改目前价格
		public function payFirstAfter($chips_id,$num){
			
			return $this->chipsDb->payFirstAfter($chips_id,$num);
		}

		//获取众筹活动状态
		public function getStart($chips_id){
			return $this->chipsDb->getStart($chips_id);
		}

		//更改众筹商品的开始状态
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
				}
				$this->chipsDb->updateChips($chips_id,$data);
			}elseif($now_time >= $start && $now_time < $end){  // 目前时间 大于开始时间 小于结束时间
				if($start != 1){
					$data['start'] = 1;
				}
				$this->chipsDb->updateChips($chips_id,$data);
			}elseif($now_time < $start && $now_time < $end){  // 目前时间小于开始时间 小于结束时间
				if($start != 3){
					$data['start'] = 3;
				}
				$this->chipsDb->updateChips($chips_id,$data);
			}
		}
	}
 ?>