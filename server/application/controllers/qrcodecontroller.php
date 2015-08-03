<?php 
	if ( ! defined('BASEPATH')) exit('No direct script access allowed');
	class QrCodeController extends CI_Controller{
		public function __construct(){
			parent::__construct();
			$this->load->model('client/clientLoginAo','clientLoginAo');
			$this->load->model('client/clientAo','clientAo');
			$this->load->model('qrcode/qrCodeAo','qrCodeAo');
			$this->load->model('user/userAppAo','userAppAo');
			$this->load->library('argv', 'argv');
		}

		//判断是否为手机
		public function is_mobile_request()  {  
		      $_SERVER['ALL_HTTP'] = isset($_SERVER['ALL_HTTP']) ? $_SERVER['ALL_HTTP'] : '';  
		      $mobile_browser = '0';  
		      if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|iphone|ipad|ipod|android|xoom)/i', strtolower($_SERVER['HTTP_USER_AGENT'])))  
		        $mobile_browser++;  
		      if((isset($_SERVER['HTTP_ACCEPT'])) and (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') !== false))  
		        $mobile_browser++;  
		      if(isset($_SERVER['HTTP_X_WAP_PROFILE']))  
		        $mobile_browser++;  
		      if(isset($_SERVER['HTTP_PROFILE']))  
		        $mobile_browser++;  
		      $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));  
		      $mobile_agents = array(  
		            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',  
		            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',  
		            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',  
		            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',  
		            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',  
		            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',  
		            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',  
		            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',  
		            'wapr','webc','winw','winw','xda','xda-' 
		            );  
		      if(in_array($mobile_ua, $mobile_agents))  
		        $mobile_browser++;  
		      if(strpos(strtolower($_SERVER['ALL_HTTP']), 'operamini') !== false)  
		        $mobile_browser++;  
		      // Pre-final check to reset everything if the user is on Windows  
		      if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows') !== false)  
		        $mobile_browser=0;  
		      // But WP7 is also Windows, with a slightly different characteristic  
		      if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'windows phone') !== false)  
		        $mobile_browser++;  
		      if($mobile_browser>0)  
		        return true;  
		      else 
		        return false;  
		}

		//缩略图
		public function thumb($img,$width,$height,$fileName){
			//源图的路径，可以是本地文件，也可以是远程图片
			$src_path = $img;
			//最终保存图片的宽
			$width = $width;
			//最终保存图片的高
			$height = $height;
			 
			//源图对象
			$src_image = imagecreatefromstring(file_get_contents($src_path));
			$src_width = imagesx($src_image);
			$src_height = imagesy($src_image);
			 
			//生成等比例的缩略图
			$tmp_image_width = 0;
			$tmp_image_height = 0;
			if ($src_width / $src_height >= $width / $height) {
			    $tmp_image_width = $width - 10;
			    $tmp_image_height = round($tmp_image_width * $src_height / $src_width);
			} else {
			    $tmp_image_height = $height - 10;
			    $tmp_image_width = round($tmp_image_height * $src_width / $src_height);
			}
			$tmp_image_width = $width - 5;
			$tmp_image_height= $height - 5;
			 
			$tmpImage = imagecreatetruecolor($tmp_image_width, $tmp_image_height);
			imagecopyresampled($tmpImage, $src_image, 0, 0, 0, 0, $tmp_image_width, $tmp_image_height, $src_width, $src_height);
			 
			//添加白边
			$final_image = imagecreatetruecolor($width, $height);
			$color = imagecolorallocate($final_image, 255, 255, 255);
			$alpha = imagecolorallocatealpha($final_image, 255, 0, 0, 100);
			imagefill($final_image, 0, 0, $color);
			 
			$x = round(($width - $tmp_image_width) / 2);
			$y = round(($height - $tmp_image_height) / 2);
			 
			imagecopy($final_image, $tmpImage, $x, $y, 0, 0, $tmp_image_width, $tmp_image_height);
			 
			//输出图片
			// header('Content-Type: image/jpeg');
			imagejpeg($final_image,$fileName);
		}

		//创建二维码
		public function create(){
			// require_once(BASEPATH.'libraries/phpqrcode/phpqrcode.php');
			$this->load->library('QrCode');
			// var_dump($this);die;
			if($_POST){
				if($this->is_mobile_request()){
					$userInfo = $this->argv->checkGet(array(
						array('userId','require'),
					));
					$userId = $userInfo['userId'];
					$clientId = $this->session->userdata('clientId');
					$mobileRequest = 1;
				}else{
					$userId = 10001;
					$clientId = 1;
					$mobileRequest = 0;
				}
				//处理上传图片
				if($this->input->post('img')){
					//设置上传的图片
					$dirName = dirName(__FILE__).'/../../../data/upload/qrcode/'.$clientId;
					if(!is_dir($dirName)){
						mkdir($dirName,0777,true);
					}
					$base64_image_content = $this->input->post('img');
					if (preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result)){
						$type = $result[2];
						$qrlogo = time().'_'.mt_rand(0,999).'.'.$type;
					  	$fileName = $dirName.'/'.$qrlogo;
					  	//保存图片
					  	file_put_contents($fileName, base64_decode(str_replace($result[1], '', $base64_image_content)));
					  	$qrlogo = '/data/upload/qrcode/'.$clientId.'/'.$qrlogo;
					}
				}else{
					echo '请选择上传的logo';die;
				}
				//处理二维码
				$username = $this->input->post('name');
				$mobile   = $this->input->post('mobile');
				$email    = $this->input->post('email');
				$company  = $this->input->post('company_name');
				$company_url = $this->input->post('company_url');
				$title    = $this->input->post('position');
				$workPhone= $this->input->post('work_phone');
				$province = $this->input->post('company_province');
				$city     = $this->input->post('company_city');
				$address  = $this->input->post('company_address');
				$info = "BEGIN:VCARD\nFN:".$username."\nTEL;CELL;VOICE:".$mobile."\nTEL;WORK;VOICE:".$workPhone."\nORG:".$company."\nADR;WORK;:;;".$address.";".$city.";".$province.";528300;中国\nTITLE:".$title."\nURL:".$company_url."\nEMAIL:".$email."\nEND:VCARD";
				$errorCorrectionLevel = "H";
				$matrixPointSize = "6";
				$margin = 5;
				//二维码图片
				$qr = time().'_'.mt_rand(0,999).'qrcode.jpg';
				$qrFileName = $dirName.'/'.$qr;
				QRcode::png($info , $qrFileName , $errorCorrectionLevel,$matrixPointSize,$margin);
				$qr = '/data/upload/qrcode/'.$clientId.'/'.$qr;
				//组合
				if($this->input->post('img')){
					//开始组合两张图片
					$QR = imagecreatefromstring(file_get_contents($qrFileName));   
				    $logo = imagecreatefromstring(file_get_contents($fileName));   
				    $QR_width = imagesx($QR);//二维码图片宽度   
				    $QR_height = imagesy($QR);//二维码图片高度   
				    $logo_width = imagesx($logo);//logo图片宽度   
				    $logo_height = imagesy($logo);//logo图片高度   
				    $logo_qr_width = $QR_width / 5;   
				    $scale = $logo_width/$logo_qr_width;
				    $logo_qr_height = $logo_height/$scale;   
				    $from_width = ($QR_width - $logo_qr_width) / 2;
				    $logo_qr_width = 78;
				    $logo_qr_height= 86;
				    $this->thumb($fileName,$logo_qr_width,$logo_qr_height,$fileName);
				    if($this->is_mobile_request()){
				    	//翻转logo图片
				    	$logo = imagecreatefromstring(file_get_contents($fileName));
				    	$rotate = imagerotate($logo,0,0);
				    	imagejpeg($rotate,$fileName);
				    }
				    $data['userId']   = $userId;
				    $data['username'] = $username;
				    $data['phone']    = $mobile;
				    $data['email']    = $email;
				    $data['company']  = $company;
				    $data['company_url'] = $company_url;
				    $data['company_address'] = $address;
				    $data['qr'] = $qr;
				    $data['logo'] = $qrlogo;
				    $data['qrX'] = $QR_width;
				    $data['qrY'] = $QR_height;
				    $result = $this->qrCodeAo->addOrMod($clientId,$data,$mobileRequest);
				    if($this->is_mobile_request()){
				    	if($result){
					    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/qrsuccess.html';
					    	header('Location:'.$url);
					    }
				    }else{
				    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/backstage/pcinfo.html?qrcodeId='.$result;
				    	header('Location:'.$url);
				    	// echo $url;
				    }
				}
			}
		}

		//创建二维码
		public function create2(){
			// echo 1;die
			// require_once(BASEPATH.'libraries/phpqrcode/phpqrcode.php');
			if($_POST){
				$userInfo = $this->argv->checkGet(array(
					array('userId','require'),
				));
				$userId = $userInfo['userId'];
				$clientId = $this->session->userdata('clientId');
				//处理上传图片
				if($_FILES){
					//设置上传目录
					$dirName = dirName(__FILE__).'/../../../data/upload/qrcode/'.$clientId;
					if(!is_dir($dirName)){
						mkdir($dirName,0777,true);
					}
					if($_FILES['img']['error'] == 0){
						$qrlogo = time().'_'.mt_rand(0,999).$_FILES['img']['name'];
						$fileName = $dirName.'/'.$qrlogo;
						move_uploaded_file($_FILES['img']['tmp_name'], $fileName);
						$qrlogo = '/data/upload/qrcode/'.$clientId.'/'.$qrlogo;
					}
				}
				//处理二维码
				$surname  = $this->input->post('surname');
				$username = $this->input->post('name');
				$mobile   = $this->input->post('mobile');
				$email    = $this->input->post('email');
				$company  = $this->input->post('company_name');
				$company_url = $this->input->post('company_url');
				$title    = $this->input->post('title');
				$province = $this->input->post('company_province');
				$city     = $this->input->post('company_city');
				$address  = $this->input->post('company_address');
				$info = "BEGIN:VCARD\nN:".$surname.";".$username."\nTEL;CELL;VOICE:".$mobile."\nORG:".$company."\nADR;WORK;:;;".$address.";".$city.";".$province.";528300;中国\nTITLE:".$title."\nURL:".$company_url."\nNOTE:单位主页:".$company_url."\nEMAIL:".$email."\nEND:VCARD";
				$errorCorrectionLevel = "H";
				$matrixPointSize = "6";
				$margin = 5;
				//二维码图片
				$qr = time().'_'.mt_rand(0,999).'qrcode.jpg';
				$qrFileName = $dirName.'/'.$qr;
				QRcode::png($info , $qrFileName , $errorCorrectionLevel,$matrixPointSize,$margin);
				$qr = '/data/upload/qrcode/'.$clientId.'/'.$qr;
				//组合
				if($_FILES){
					//开始组合两张图片
					$QR = imagecreatefromstring(file_get_contents($qrFileName));   
				    $logo = imagecreatefromstring(file_get_contents($fileName));   
				    $QR_width = imagesx($QR);//二维码图片宽度   
				    $QR_height = imagesy($QR);//二维码图片高度   
				    $logo_width = imagesx($logo);//logo图片宽度   
				    $logo_height = imagesy($logo);//logo图片高度   
				    $logo_qr_width = $QR_width / 5;   
				    $scale = $logo_width/$logo_qr_width;
				    $logo_qr_height = $logo_height/$scale;   
				    $from_width = ($QR_width - $logo_qr_width) / 2;
				    $logo_qr_width = 78;
				    $logo_qr_height= 86;
				    $this->thumb($fileName,$logo_qr_width,$logo_qr_height,$fileName);
				    if($this->is_mobile_request()){
				    	//翻转logo图片
				    	$logo = imagecreatefromstring(file_get_contents($fileName));
				    	$rotate = imagerotate($logo,0,0);
				    	imagejpeg($rotate,$fileName);
				    }
				    $data['userId']   = $userId;
				    $data['username'] = $surname.$username;
				    $data['phone']    = $mobile;
				    $data['email']    = $email;
				    $data['company']  = $company;
				    $data['company_url'] = $company_url;
				    $data['company_address'] = $address;
				    $data['qr'] = $qr;
				    $data['logo'] = $qrlogo;
				    $data['qrX'] = $QR_width;
				    $data['qrY'] = $QR_height;
				    $result = $this->qrCodeAo->addOrMod($clientId,$data);
				    if($result){
				    	$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/qrsuccess.html';
				    	header('Location:'.$url);
				    }
				}
			}
		}

		/**
		 * @view json
		 * 获取二维码信息
		 */
		public function getQrcodeInfo(){
			$clientId = $this->session->userdata('clientId');
			if($this->input->is_ajax_request()){
				return $this->qrCodeAo->getQrcodeInfo($clientId)[0];
			}
		}

		/**
		 * @view json
		 * 获取别人二维码信息
		 */
		public function getOtherQrcodeInfo(){
			if($this->input->is_ajax_request()){
				$clientId = $this->input->post('clientId');
				$result = $this->qrCodeAo->getQrcodeInfo($clientId);
				if($result){
					return $result[0];
				}else{
					throw new CI_MyException(1,'没有该条记录');
				}
			}
		}

		/**
		 * @view json
		 * 获取二维码信息
		 */
		public function getInfo(){
			$qrcodeId = $this->input->post('qrcodeId');
			return $this->qrCodeAo->getInfo($qrcodeId);
		}

		/**
		 * @view json
		 * 获取分享信息
		 */
		public function getShareInfo(){
			if($this->input->is_ajax_request()){
				$userInfo = $this->argv->checkGet(array(
					array('userId','require'),
				));
				$userId = $userInfo['userId'];
				$clientId = $this->session->userdata('clientId');
				$qrcode   = $this->session->userdata('qrcode');
				$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/qrsuccess.html?clientId='.$clientId;
				$qrCodeInfo = $this->qrCodeAo->getQrcodeInfo($clientId)[0];
				$arr['title'] = '这是我的电子名片';
				$arr['link']  = $url;
				$arr['imgUrl']= $qrcode;
				return $arr;
			}
		}

		/**
		 * @view json
		 */
		public function getAllInfo(){
			if($this->input->is_ajax_request()){
				$userId = $this->session->userdata('userId');
				$dataWhere = $this->argv->checkGet(array(
					array('username','option'),
				));
				$dataLimit = $this->argv->checkGet(array(
					array('pageIndex','require'),
					array('pageSize','require'),
				));
				return $this->qrCodeAo->getAllInfo($userId,$dataWhere,$dataLimit);
			}
		}

		/**
		 * @view json
		 */
		public function getCreateUrl(){
			if($this->input->is_ajax_request()){
				$data = $this->argv->checkGet(array(
					array('userId','require'),
				));
				$userId = $data['userId'];
				$url = 'http://'.$_SERVER['HTTP_HOST'].'/'.$userId.'/qrcode.html';
				return $url;
			}
		}

		public function createCode(){
			$this->load->library('http');
			$data = $this->argv->checkGet(array(
				array('userId','require'),
			));
			$userId = $data['userId'];
			$info   = $this->userAppAo->getTokenAndTicket($userId);
			$access_token = $info['appAccessToken'];
			$url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;
			$arr['action_name'] = 'QR_SCENE';
			$arr['expire_seconds'] = 7200;
			$httpResponse = $this->http->ajax(array(
				'url'=>$url,
				'type'=>'post',
				'data'=>json_encode($arr),
				'dataType'=>'plain',
				'responseType'=>'json'
			));
			// var_dump($httpResponse);die;
			$ticket = $httpResponse['body']['ticket'];

			$url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);
			$result = $this->Get($url);
			// return $result;
			echo $url;
			// var_dump($result);die;
		}

		public function Get($url){
		    if(function_exists('file_get_contents')){
		        $file_contents = file_get_contents($url);
		    }else{
		        $ch = curl_init();
		        $timeout = 5;
		        curl_setopt ($ch, CURLOPT_URL, $url);
		        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		        $file_contents = curl_exec($ch);
		        curl_close($ch);
		    }
		    return $file_contents;
		}

		/**
		 * @view json
		 * 判断有无关注
		 */
		public function judgeSub(){
			if($this->input->is_ajax_request()){
				$data = $this->argv->checkGet(array(
					array('userId','require'),
				));
				$userId = $data['userId'];
				$clientId = $this->session->userdata('clientId');
				return $this->clientAo->judgeSub($userId,$clientId);
			}
		}

		public function test(){
			return 'xx';
		}
	}
 ?>