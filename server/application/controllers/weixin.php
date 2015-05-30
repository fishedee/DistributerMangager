<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Weixin extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
	}
	
	public function index(){
	/**
	 * 微信接入验证
	 * 在入口进行验证而不是放到框架里验证，主要是解决验证URL超时的问题
	 */
	define("TOKEN", "weiyd");
	if (! empty ( $_GET ['echostr'] ) && ! empty ( $_GET ["signature"] ) && ! empty ( $_GET ["nonce"] )) {
        	$signature = $_GET ["signature"];
        	$timestamp = $_GET ["timestamp"];
        	$nonce = $_GET ["nonce"];
	
        	$tmpArr = array (
                	        TOKEN,
                        	$timestamp,
                        	$nonce
        	);
        	sort ( $tmpArr, SORT_STRING );
        	$tmpStr = sha1 ( implode ( $tmpArr ) );

        	if ($tmpStr == $signature) {
                echo $_GET ["echostr"];
        	}
        	exit ();
	}


		//触发事件,触发那个事件找那个表,根据微信号找要回复内容，回复类型

		/*测试*/
//  $xmlTpl = "<xml><ToUserName><![CDATA[gh_4d62ec168bf2]]></ToUserName>
// <FromUserName><![CDATA[oPg08s9FLrGVp21zNT0EyZ_sG6eA]]></FromUserName>
// <CreateTime>1432715265</CreateTime>
// <MsgType><![CDATA[event]]></MsgType>
// <Event><![CDATA[subscribe]]></Event>
// </xml>";

		//$this->load->model('weixin/wxreply','wxreply');
		//$data2=$this->wxreply->responseMsg($xmlTpl);
		//file_put_contents(dirname(__FILE__).'/out.text', print_r($data2).'1');
		//return $data2;
		/*测试end*/


		$this->load->model('weixin/wxreply','wxreply');
		return $this->wxreply->responseMsg();


		}
		
}
