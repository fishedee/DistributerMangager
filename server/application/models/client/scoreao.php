<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ScoreAo extends CI_Model {

	private $scoreEvent= array(
		'CHECK_IN'=>'1',
		'ENJOY_CIRCLE'=>'2',
		'ENJOY_FRIEND'=>'3',
		'AKS_DISTRIBUTION'=>'4',
		'ENJOY_DOWN'=>'5',
		'EXCHANGE'=>'6',
		'SALE'=>'7',
		'SALE_DOWN'=>'8',
		'BUY'=>9,
		);

	private $systemScore = array(
		'CHECK_IN'=>10,
		'ENJOY_CIRCLE'=>10,
		'ENJOY_FRIEND'=>20,
		'AKS_DISTRIBUTION'=>10,
		'ENJOY_DOWN'=>2,
		'SALE'=>10,
		'SALE_DOWN'=>10
		);

	public function __construct(){
		parent::__construct();
		$this->load->model('client/scoreDb','scoreDb');
		$this->load->model('user/userAppAo', 'userAppAo');
		$this->load->model('client/clientAo','clientAo');
		$this->load->model('user/userAo','userAo');
		$this->load->model('distribution/distributionAo','distributionAo');
		$this->load->model('client/remindDb','remindDb');
		$this->load->model('distribution/distributionConfigAo','distributionConfigAo');
		$this->load->model('distribution/distributionConfigEnum','distributionConfigEnum');
	}

	//更改积分配置
	private function changeScore($userId){
		$config = $this->distributionConfigAo->getConfig($userId);
		if($config){
			$this->systemScore['CHECK_IN'] = $config['checkin'];
			$this->systemScore['ENJOY_CIRCLE'] = $config['circle'];
			$this->systemScore['ENJOY_FRIEND'] = $config['friend'];
			$this->systemScore['AKS_DISTRIBUTION'] = $config['ask'];
			$this->systemScore['ENJOY_DOWN']   = $config['enjoydown'];
		}
	}

	//签到
	public function checkIn($ToUserName,$openId){
		$userId = $this->userAppAo->getUserId($ToUserName);
		$data['openId'] = $openId;
		$data['userId'] = $userId;
		$data['type']   = 2;
		$clientId = $this->clientAo->addOnce($data);
		$result = $this->checkInToday($clientId);
		$this->changeScore($userId);
		if($result){
			return '您今天已经签到了,请明天再来';
		}else{
			//判断还有多少积分
			$userInfo = $this->userAo->get($userId);
			$venderScore = $userInfo['score'];
			if($venderScore < 100){
				$result = $this->remindDb->checkScore($userId);
				$code   = 100;
				if(!$result){
					//发送短信
					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
					$smsConf = array(
					    'key'   => '296b7022d952229e9ec0b64a4d227420', //您申请的APPKEY
					    'mobile'    => $userInfo['phone'], //接受短信的用户手机号码
					    'tpl_id'    => '6396', //您申请的短信模板ID，根据实际情况修改
					    'tpl_value' =>'#code#='.$code.'&#company#=微易点' //您设置的模板变量，根据实际情况修改
					);
					$content = $this->juhecurl($sendUrl,$smsConf,1);
					if($content){
						$result = json_decode($content,true);
						$error_code = $result['error_code'];
						if($error_code == 0){
							//插入记录
							$data = array();
							$data['userId'] = $userId;
							$this->remindDb->add($data);
						}
					}
				}
			}
			if($userInfo['score'] < $this->systemScore['CHECK_IN']){
				return '该商家的积分不足,不能分配积分';
			}else{
				//还没签到可以进行签到
				$data = array();
				$data['vender']   = $userId;
				$data['clientId'] = $clientId;
				$data['event']    = $this->scoreEvent['CHECK_IN'];
				$data['createTime'] = date('Y-m-d H:i:s',time());
				$data['remark']   = '签到送积分';
				$data['score']    = $this->systemScore['CHECK_IN'];
				$result = $this->scoreDb->checkIn($data);
				if($result){
					$clientInfo = $this->clientAo->get($userId,$clientId);
					$score      = $clientInfo['score'];
					$data = array();
					$data['score']  = $score + $this->systemScore['CHECK_IN'];
					$result = $this->clientAo->mod($userId,$clientId,$data);
					if($result){
						$data = array();
						$data['score'] = $userInfo['score'] - $this->systemScore['CHECK_IN'];
						$this->userAo->mod($userId,$data);
						return "恭喜您签到成功,赠送".$this->systemScore['CHECK_IN']."积分!";
					}else{
						return '积分变更失败,请联系客服';
					}
				}else{
					return '签到失败,请稍后再尝试';
				}
			}
		}
	}

	//判断今日签到与否
	public function checkInToday($clientId){
		$event = $this->scoreEvent['CHECK_IN'];
		return $this->scoreDb->checkInToday($clientId,$event);
	}

	//判断今日分享到朋友全的页面
	public function checkEnjoyShareToday($clientId,$url){
		$event = $this->scoreEvent['ENJOY_CIRCLE'];
		return $this->scoreDb->checkEnjoyShareToday($clientId,$url,$event);
	}

	//判断今日分享到朋友的页面
	public function checkEnjoyFriendToday($clientId,$url){
		$event = $this->scoreEvent['ENJOY_FRIEND'];
		return $this->scoreDb->checkEnjoyFriendToday($clientId,$url,$event);
	}

	//获取积分日志
	public function getLog($clientId){
		$result = $this->scoreDb->getLog($clientId);
		foreach ($result as $key => $value) {
			if($value['dis'] == 1){
				$result[$key]['dis'] = '+';
			}else{
				$result[$key]['dis'] = '-';
			}
		}
		return $result;
	}

	//获取分享朋友圈的必要参数
	public function getEnjoyParameters($userId,$url){
		$userAppInfo = $this->userAppAo->get($userId);
		$title = $userAppInfo['appName'];
		$url   = $url;
		$imgUrl= $userAppInfo['appLogo'];
		return array(
			'title'=>$title,
			'url'  =>$url,
			'imgUrl'=>$imgUrl
			);
	}

	//分享朋友圈成功
	public function enjoyShareSuccess($userId,$clientId,$url){
		$this->changeScore($userId);
		$result = $this->checkEnjoyShareToday($clientId,$url);
		if($result){
			return 0;
		}else{
			//判断商家积分是否充值
			$userInfo = $this->userAo->get($userId);
			$venderScore = $userInfo['score'];

			if($venderScore < 100){
				$result = $this->remindDb->checkScore($userId);
				$code   = 100;
				if(!$result){
					//发送短信
					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
					$smsConf = array(
					    'key'   => '296b7022d952229e9ec0b64a4d227420', //您申请的APPKEY
					    'mobile'    => $userInfo['phone'], //接受短信的用户手机号码
					    'tpl_id'    => '6396', //您申请的短信模板ID，根据实际情况修改
					    'tpl_value' =>'#code#='.$code.'&#company#=微易点' //您设置的模板变量，根据实际情况修改
					);
					$content = $this->juhecurl($sendUrl,$smsConf,1);
					if($content){
						$result = json_decode($content,true);
						$error_code = $result['error_code'];
						if($error_code == 0){
							//插入记录
							$data = array();
							$data['userId'] = $userId;
							$this->remindDb->add($data);
						}
					}
				}
			}

			if($venderScore > $this->systemScore['ENJOY_CIRCLE'] + $this->scoreEvent['ENJOY_DOWN']){
				//今日还没有分享
				$data = array();
				$data['vender']   = $userId;
				$data['clientId'] = $clientId;
				$data['event']    = $this->scoreEvent['ENJOY_CIRCLE'];
				$data['createTime'] = date('Y-m-d H:i:s',time());
				$data['remark']   = '分享朋友圈送积分';
				$data['score']    = $this->systemScore['ENJOY_CIRCLE'];
				$data['enjoyUrl'] = $url;
				$result = $this->scoreDb->checkIn($data);
				if($result){
					$clientInfo = $this->clientAo->get($userId,$clientId);
					$score      = $clientInfo['score'];
					$data = array();
					$data['score']  = $score + $this->systemScore['ENJOY_CIRCLE'];
					$result = $this->clientAo->mod($userId,$clientId,$data);
					//商家扣除积分
					$data = array();
					$dis_one_score = $venderScore - $this->systemScore['ENJOY_CIRCLE'];
					$data['score'] = $dis_one_score;
					$this->userAo->mod($userId,$data);
					if($result){
						//成功分享 上一级获取积分
						$userInfo = $this->userAo->checkUserClientId($clientId);
						if($userInfo){
							$myUserId = $userInfo[0]['userId'];
							$xx = $this->distributionAo->getUp($userId,$myUserId);
							if($xx[0]['scort'] > 1){
								$upUserInfo = $this->distributionAo->checkUp($userId,$myUserId);
								if($upUserInfo){
									$upUserId = $upUserInfo[0]['upUserId'];
									$upUserInfo = $this->userAo->get($upUserId);
									$upUserClientId = $upUserInfo['clientId'];
									$data = array();
									$data['clientId'] = $upUserClientId;
									$data['event']    = $this->scoreEvent['ENJOY_DOWN'];
									$data['createTime'] = date('Y-m-d H:i:s',time());
									$data['remark']   = '下级分享朋友圈';
									$data['score']    = $this->systemScore['ENJOY_DOWN'];
									$data['enjoyUrl'] = $url;
									$result = $this->scoreDb->checkIn($data);
									if($result){
										$clientInfo = $this->clientAo->get($userId,$upUserClientId);
										$score      = $clientInfo['score'];
										$data = array();
										$data['score']  = $score + $this->systemScore['ENJOY_DOWN'];
										$result = $this->clientAo->mod($userId,$upUserClientId,$data);
										//成功增加积分 商家扣除
										$data = array();
										$data['score'] = $dis_one_score - $this->systemScore['ENJOY_DOWN'];
										$this->userAo->mod($userId,$data);
									}
								}
							}
						}
						return $result;
					}else{
						throw new CI_MyException(1,'积分日志增加成功,积分增加失败');
					}
				}else{
					throw new CI_MyException(1,'分享到朋友圈送积分失败');
				}
			}else{
				throw new CI_MyException(1,'该商家的积分不足,不能分配积分');
			}
		}
	}

	//分享朋友成功
	public function enjoyFriendSuccess($userId,$clientId,$url){
		$this->changeScore($userId);
		$result = $this->checkEnjoyFriendToday($clientId,$url);
		if($result){
			return 0;
		}else{
			//判断商家 积分是否足够
			$userInfo = $this->userAo->get($userId);
			$venderScore = $userInfo['score'];

			if($venderScore < 100){
				$result = $this->remindDb->checkScore($userId);
				$code   = 100;
				if(!$result){
					//发送短信
					$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
					$smsConf = array(
					    'key'   => '296b7022d952229e9ec0b64a4d227420', //您申请的APPKEY
					    'mobile'    => $userInfo['phone'], //接受短信的用户手机号码
					    'tpl_id'    => '6396', //您申请的短信模板ID，根据实际情况修改
					    'tpl_value' =>'#code#='.$code.'&#company#=微易点' //您设置的模板变量，根据实际情况修改
					);
					$content = $this->juhecurl($sendUrl,$smsConf,1);
					if($content){
						$result = json_decode($content,true);
						$error_code = $result['error_code'];
						if($error_code == 0){
							//插入记录
							$data = array();
							$data['userId'] = $userId;
							$this->remindDb->add($data);
						}
					}
				}
			}
			if($venderScore > $this->systemScore['ENJOY_FRIEND'] + $this->scoreEvent['ENJOY_DOWN']){
				//今日还没有分享
				$data = array();
				$data['vender']   = $userId;
				$data['clientId'] = $clientId;
				$data['event']    = $this->scoreEvent['ENJOY_FRIEND'];
				$data['createTime'] = date('Y-m-d H:i:s',time());
				$data['remark']   = '分享给朋友送积分';
				$data['score']    = $this->systemScore['ENJOY_FRIEND'];
				$data['enjoyUrl'] = $url;
				$result = $this->scoreDb->checkIn($data);
				if($result){
					$clientInfo = $this->clientAo->get($userId,$clientId);
					$score      = $clientInfo['score'];
					$data = array();
					$data['score']  = $score + $this->systemScore['ENJOY_FRIEND'];
					$result = $this->clientAo->mod($userId,$clientId,$data);
					//成功分享给朋友 商家扣除积分
					$data = array();
					$dis_one_score = $venderScore - $this->systemScore['ENJOY_FRIEND'];
					$data['score'] = $dis_one_score;
					$this->userAo->mod($userId,$data);
					if($result){
						//成功分享 上一级获取积分
						$userInfo = $this->userAo->checkUserClientId($clientId);
						if($userInfo){
							$myUserId = $userInfo[0]['userId'];
							$xx = $this->distributionAo->getUp($userId,$myUserId);
							if($xx[0]['scort'] > 1){
								$upUserInfo = $this->distributionAo->checkUp($userId,$myUserId);
								if($upUserInfo){
									$upUserId = $upUserInfo[0]['upUserId'];
									$upUserInfo = $this->userAo->get($upUserId);
									$upUserClientId = $upUserInfo['clientId'];
									$data = array();
									$data['clientId'] = $upUserClientId;
									$data['event']    = $this->scoreEvent['ENJOY_DOWN'];
									$data['createTime'] = date('Y-m-d H:i:s',time());
									$data['remark']   = '下级分享给朋友';
									$data['score']    = $this->systemScore['ENJOY_DOWN'];
									$data['enjoyUrl'] = $url;
									$result = $this->scoreDb->checkIn($data);
									if($result){
										$clientInfo = $this->clientAo->get($userId,$upUserClientId);
										$score      = $clientInfo['score'];
										$data = array();
										$data['score']  = $score + $this->systemScore['ENJOY_DOWN'];
										$result = $this->clientAo->mod($userId,$upUserClientId,$data);
										//成功增加积分 商家扣除
										$data = array();
										$data['score'] = $dis_one_score - $this->systemScore['ENJOY_DOWN'];
										$this->userAo->mod($userId,$data);
									}
								}
							}
						}
						return $result;
					}else{
						throw new CI_MyException(1,'积分日志增加成功,积分增加失败');
					}
				}else{
					throw new CI_MyException(1,'分享给朋友送积分失败');
				}
			}else{
				throw new CI_MyException(1,'该商家的积分不足,不能分配积分');
			}
		}
	}

	//申请分销增加积分
	public function askDistribution($vender,$upUserClientId){
		$this->changeScore($vender);
		$userInfo = $this->userAo->get($vender);
		$venderScore = $userInfo['score'];
		if($venderScore < 100){
			$result = $this->remindDb->checkScore($vender);
			$code   = 100;
			if(!$result){
				//发送短信
				$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
				$smsConf = array(
				    'key'   => '296b7022d952229e9ec0b64a4d227420', //您申请的APPKEY
				    'mobile'    => $userInfo['phone'], //接受短信的用户手机号码
				    'tpl_id'    => '6396', //您申请的短信模板ID，根据实际情况修改
				    'tpl_value' =>'#code#='.$code.'&#company#=微易点' //您设置的模板变量，根据实际情况修改
				);
				$content = $this->juhecurl($sendUrl,$smsConf,1);
				if($content){
					$result = json_decode($content,true);
					$error_code = $result['error_code'];
					if($error_code == 0){
						//插入记录
						$data = array();
						$data['userId'] = $vender;
						$this->remindDb->add($data);
					}
				}
			}
		}
		if($userInfo['score'] > $this->systemScore['AKS_DISTRIBUTION']){
			$data = array();
			$data['vender']   = $vender;
			$data['clientId'] = $upUserClientId;
			$data['event']    = $this->scoreEvent['AKS_DISTRIBUTION'];
			$data['createTime'] = date('Y-m-d H:i:s',time());
			$data['remark']   = '增加一名一级会员';
			$data['score']    = $this->systemScore['AKS_DISTRIBUTION'];
			$data['enjoyUrl'] = $url;
			$result = $this->scoreDb->checkIn($data);
			if($result){
				$clientInfo = $this->clientAo->get($vender,$upUserClientId);
				$score      = $clientInfo['score'];
				$data = array();
				$data['score']  = $score + $this->systemScore['AKS_DISTRIBUTION'];
				$result = $this->clientAo->mod($vender,$upUserClientId,$data);
				$data = array();
				$data['score'] = $userInfo['score'] - $this->systemScore['AKS_DISTRIBUTION'];
				$this->userAo->mod($vender,$data);
			}
		}
	}

	private function juhecurl($url,$params=false,$ispost=0){
	    $httpInfo = array();
	    $ch = curl_init();
	 
	    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
	    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
	    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
	    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
	    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
	    if( $ispost )
	    {
	        curl_setopt( $ch , CURLOPT_POST , true );
	        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
	        curl_setopt( $ch , CURLOPT_URL , $url );
	    }
	    else
	    {
	        if($params){
	            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
	        }else{
	            curl_setopt( $ch , CURLOPT_URL , $url);
	        }
	    }
	    $response = curl_exec( $ch );
	    if ($response === FALSE) {
	        //echo "cURL Error: " . curl_error($ch);
	        return false;
	    }
	    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
	    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
	    curl_close( $ch );
	    return $response;
	}

	/**
	 * 特殊分销 普通会员卖出去 增加积分
	 * date:2015.11.30
	 */
	public function sale($userId,$config,$downClientId,$upClientId){
		$userInfo = $this->userAo->get($userId);
		if($config == 0){
			$preScore = $this->systemScore['SALE'] + $this->systemScore['SALE_DOWN'];
			$downScore= $this->systemScore['SALE'];
			$upScore  = $this->systemScore['SALE_DOWN'];
		}else{
			$preScore = $config['commonDownScore'] + $config['commonUpScore'];
			$downScore= $config['commonDownScore'];
			$upScore  = $config['commonUpScore'];
		}
		if($userInfo['score'] > $preScore){
			//分配积分
			$downClientInfo = $this->clientAo->get($userId,$downClientId);
			$upClientInfo   = $this->clientAo->get($userId,$upClientId);
			//更新本级
			$data['score']  = $downClientInfo['score'] + $downScore;
			$this->clientAo->mod($userId,$downClientId,$data);
			//更新上一级
			$data['score']  = $upClientInfo['score'] + $upScore;
			$this->clientAo->mod($userId,$upClientId,$data);
			//写入积分日志 本级
			$data = array();
			$data['vender']   = $userId;
			$data['clientId'] = $downClientId;
			$data['event']    = $this->scoreEvent['SALE'];
			$data['createTime'] = date('Y-m-d H:i:s',time());
			$data['remark']   = '普通会员销售';
			$data['score']    = $this->systemScore['SALE'];
			$this->scoreDb->checkIn($data);
			//写入积分日志 上一级
			$data = array();
			$data['vender']   = $userId;
			$data['clientId'] = $upClientId;
			$data['event']    = $this->scoreEvent['SALE_DOWN'];
			$data['createTime'] = date('Y-m-d H:i:s',time());
			$data['remark']   = '下级销售';
			$data['score']    = $this->systemScore['SALE_DOWN'];
			$this->scoreDb->checkIn($data);
			//扣除商家积分
			$data = array();
			$data['score'] = $userInfo['score'] - $preScore;
			$this->userAo->mod($userId,$data);
		}
	}

	/**
	 * @购买抵消积分
	 * date:2015.12.08
	 */
	public function buy($userId,$clientId,$score){
		$data = array();
		$data['vender']   = $userId;
		$data['clientId'] = $clientId;
		$data['event']    = $this->scoreEvent['BUY'];
		$data['createTime'] = date('Y-m-d H:i:s',time());
		$data['remark']   = '积分抵消金额';
		$data['score']    = $score;
		$data['dis']      = 0;
		$this->scoreDb->checkIn($data);
	}

}
