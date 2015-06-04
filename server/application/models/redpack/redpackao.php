<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class RedPackAo extends CI_Model 
{
	public function __construct(){
		parent::__construct();
		$this->load->model('redpack/redPackDb','redPackDb');
		$this->load->model('redpack/redPackClientDb','redPackClientDb');
		$this->load->model('redpack/redPackStateEnum','redPackStateEnum');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('user/userAppAo','userAppAo');
		$this->load->model('common/commonErrorEnum','commonErrorEnum');
	}

	private function addOnceSetting($userId){
		$redPacks = $this->redPackDb->getByUserId($userId);
		if( count($redPacks) == 0 ){
			$this->redPackDb->add(array(
				'userId'=>$userId,
				'state'=>$this->redPackStateEnum->CLOSE
			));
		}
	}

	private function filterInput($data){
		$data['minMoney'] = intval($data['minMoneyShow']*100);
		$data['maxMoney'] = intval($data['maxMoneyShow']*100);
		unset($data['minMoneyShow']);
		unset($data['maxMoneyShow']);
		return $data;
	}

	private function filterOutput($data){
		$data['minMoneyShow'] = sprintf('%.2f',$data['minMoney']/100);
		$data['maxMoneyShow'] = sprintf('%.2f',$data['maxMoney']/100);
		$data['link'] = 'http://'.$data['userId'].'.'.$_SERVER['HTTP_HOST'].'/'.$data['userId'].'/redpack.html';
		return $data;
	}

	public function getSetting($userId){
		$this->addOnceSetting($userId);

		$data = $this->redPackDb->getByUserId($userId)[0];

		$data['packNum'] = $this->redPackClientDb->getCountByRedPackId($data['redPackId']);

		$data = $this->filterOutput($data);

		return $data;
	}

	public function setSetting( $userId,$data ){
		$this->addOnceSetting($userId);

		$data = $this->filterInput($data);

		if($data['state'] == $this->redPackStateEnum->OPEN ){
			if( trim($data['nickName']) == '' )
				throw new CI_MyException(1,'请输入商户名称');
			if( trim($data['wishing']) == '' )
				throw new CI_MyException(1,'请输入祝福语');
			if( trim($data['actName']) == '' )
				throw new CI_MyException(1,'请输入活动名称');
			if( trim($data['remark']) == '' )
				throw new CI_MyException(1,'请输入备注');
			if( trim($data['minMoney']) == '' || $data['minMoney'] < 100 )
				throw new CI_MyException(1,'请输入最小为1元的红包金额');
			if( trim($data['maxMoney']) == '' || $data['maxMoney'] > 20000 )
				throw new CI_MyException(1,'请输入最大为200元的红包金额');
			if( $data['minMoney'] > $data['maxMoney'] )
				throw new CI_MyException(1,'最小红包金额需要少于或等于最大红包金额');
			if( intval($data['maxPackNum']) <= 0 )
				throw new CI_MyException(1,'最大红包数量应该是大于0的');

			if( trim($data['redPackRuleImage']) == '' )
				throw new CI_MyException(1,'请输入红包说明的图片');
			if( trim($data['redPackNoneTip']) == '')
				throw new CI_MyException(1,'请输入红包发完时的提示');

			$userApp = $this->userAppAo->get($userId);

			$this->userAppAo->checkAll($userApp);
		}
		
		$this->redPackDb->modByUserId($userId,$data);
	}

	public function searchRedPack( $userId,$limit){
		$redPackInfo = $this->getSetting($userId);
		return $this->redPackClientDb->search(array(
			'redPackId'=>$redPackInfo['redPackId']
		),$limit);
	}

	public function getRedPack( $userId,$clientId){
		$redPack = array(
			'canGet'=>true,
			'hasGet'=>false,
			'canGetTip'=>''
		);
		$redPackInfo = $this->getSetting($userId);
		if( $redPackInfo['state'] != $this->redPackStateEnum->OPEN){
			$redPack['canGet'] = false;
			$redPack['canGetTip'] = '企业没有开通红包呢';
		}else if( $redPackInfo['packNum'] >= $redPackInfo['maxPackNum'] ){
			$redPack['canGet'] = false;
			$redPack['canGetTip'] = $redPackInfo['redPackNoneTip'];
		}else{
			$nowHour = intval(date('H',time()));
			if( $nowHour >= 0 && $nowHour <= 8 ){
				$redPack['canGet'] = false;
				$redPack['canGetTip'] = '早上0点到8点之间不能发放红包噢';
			}
		}

		$redPackClientInfo = $this->redPackClientDb->search(array(
			'redPackId'=>$redPackInfo['redPackId'],
			'clientId'=>$clientId
		),array());

		if($redPackClientInfo['count'] != 0 ){
			$redPack['canGet'] = false;
			$redPack['hasGet'] = true;
			$redPack['canGetTip'] = '你已经领取过该红包了';
		}

		return array_merge($redPackInfo,$redPack);
	}

	public function tryRedPack( $userId, $clientId ){
		//初始化userApp
		$appInfo = $this->userAppAo->get($userId);

    	$this->userAppAo->checkAll($appInfo);

		$this->load->library('wxSdk',array(
			'appId'=>$appInfo['appId'],
			'appKey'=>$appInfo['appKey'],
			'mchId'=>$appInfo['mchId'],
			'mchKey'=>$appInfo['mchKey'],
			'mchSslCert'=>$appInfo['mchSslCert'],
			'mchSslKey'=>$appInfo['mchSslKey']
		),'wxSdk');

		$client = $this->clientAo->get($userId,$clientId);

		//计算发放金额
		$redPackInfo = $this->getRedPack($userId,$clientId);
		if( $redPackInfo['canGet'] == false )
			throw new CI_MyException(1,$redPackInfo['canGetTip']);

		$money = mt_rand($redPackInfo['minMoney'],$redPackInfo['maxMoney']);

		$orderId = $appInfo['mchId'].date('YmdHis').rand(1000,9999);

		//添加微信的红包结果
		$this->wxSdk->sendRedPack(
			$client['openId'],
			$money,
			$orderId,
			$redPackInfo['nickName'],
			$redPackInfo['wishing'],
			$redPackInfo['actName'],
			$redPackInfo['remark']
		);

		//添加本地的红包结果
		$this->redPackClientDb->add(array(
			'redPackId'=>$redPackInfo['redPackId'],
			'clientId'=>$clientId,
			'money'=>$money
		));

		//更新最新的红包信息
		$redPackInfo = $this->getRedPack($userId,$clientId);

		return array_merge(
			$redPackInfo,
			array(
				'money'=>$money,
				'moneyShow'=>sprintf('%.2f',$money/100)
			)
		);
	}

}
