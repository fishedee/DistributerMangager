<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BookingAo extends CI_Model {

	private $option = array(
		'NOT'=>0,
		'ACCEPT'=>1,
		'FORBID'=>2
		);

	public function __construct(){
		parent::__construct();
		$this->load->model('board/bookingDb','bookingDb');
		$this->load->model('board/boardDateAo','boardDateAo');
		$this->load->model('board/boardTypeAo','boardTypeAo');
	}

	public function search($userId,$dataWhere,$dataLimit){
		$dataWhere['userId'] = $userId;
		$result = $this->bookingDb->search($dataWhere,$dataLimit);
		return $result;
	}

	//预定
	public function booking($userId,$clientId,$data){

		//检测验证码
		if($data['code'] != $this->session->userdata('booking_phone_code')){
			throw new CI_MyException(1,'验证码错误');
		}

		if($userId != $data['userId']){
			throw new CI_MyException(1,'用户id非法');
		}
		//查询时间
		$time = $this->boardDateAo->getDate($userId,$data['confirmTime'])['time'];
		$bookingTime = $data['confirmDay'].' '.$time;
		if(strtotime($bookingTime) < time()){
			throw new CI_MyException(1,'预定时间非法');
		}
		//查看类型
		$boardType = $this->boardTypeAo->getType($userId,$data['confirmType']);
		$arr['userId'] = $userId;
		$arr['clientId'] = $clientId;
		$arr['boardTypeId'] = $data['confirmType'];
		$arr['bookingTime'] = $bookingTime;
		$arr['people'] = $data['peopleNum'];
		$arr['remark'] = $data['special'];
		$arr['name']   = $data['name'];
		$arr['phone']  = $data['phone'];
		return $this->bookingDb->add($arr);
	}

	//获取订单信息
	public function getBookingInfo($userId,$bookingId){
		$info = $this->bookingDb->getBookingInfo($bookingId);
		if($info){
			$info = $info[0];
			if($info['userId'] != $userId){
				throw new CI_MyException(1,'无效查看');
			}
			return $info;
		}else{
			throw new CI_MyException(1,'无效预定id');
		}
	}

	//删除
	public function del($userId,$bookingId){
		$info = $this->getBookingInfo($userId,$bookingId);
		return $this->bookingDb->del($bookingId);
	}

	//受理
	public function accept($userId,$bookingId){
		$info = $this->getBookingInfo($userId,$bookingId);
		if($info['state'] != $this->option['NOT']){
			throw new CI_MyException(1,'该预定不处于可受理状态');
		}
		$data['state'] = $this->option['ACCEPT'];
		return $this->bookingDb->mod($bookingId,$data);
	}

	//检测拒绝资格
	public function checkForbid($userId,$bookingId){
		$info = $this->getBookingInfo($userId,$bookingId);
		if($info['state'] != $this->option['NOT']){
			throw new CI_MyException(1,'该预定不处于可受理状态');
		}
		return 1;
	}

	//拒绝
	public function forbid($userId,$bookingId,$data){
		$info = $this->checkForbid($userId,$bookingId);
		$arr['state'] = $this->option['FORBID'];
		$arr['reason']  = $data['data']['reason'];
		return $this->bookingDb->mod($bookingId,$arr);
	}
}
