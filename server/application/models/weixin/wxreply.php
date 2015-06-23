<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Wxreply extends CI_Model {
    //验证签名
    public function valid()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $token = TOKEN;
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if($tmpStr == $signature){
            echo $echoStr;
            exit;
        }
    }

    //响应消息
    public function responseMsg($xmlTpl=null)
    {
    	
        if(empty($xmlTpl)){
        	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        }else {
        	$postStr = $xmlTpl;
        }
    	//$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    	//print_r($postStr);die();
       //file_put_contents(dirname(__FILE__).'/come.text',var_export($postStr,TRUE));
        if (!empty($postStr)){
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $this->fenlei($postObj);
            //return $postObj;
        }else {
            echo "";
            exit;
        }
    }

    //消息类型分离
    public function fenlei($postObj){
    	$RX_TYPE = trim($postObj->MsgType);
    	switch ($RX_TYPE)
    	{
    		case "event":
    			$result = $this->receiveEvent($postObj);
    			break;
    		case "text":
    			$result = $this->receiveText($postObj);
    			break;
    		case "image":
    			$result = $this->receiveImage($postObj);
    			break;
    		case "location":
    			$result = $this->receiveLocation($postObj);
    			break;
    		case "voice":
    			$result = $this->receiveVoice($postObj);
    			break;
    		case "video":
    			$result = $this->receiveVideo($postObj);
    			break;
    		case "link":
    			$result = $this->receiveLink($postObj);
    			break;
    		default:
    			$result = "unknown msg type: ".$RX_TYPE;
    			break;
    	}
    	file_put_contents(dirname(__FILE__).'/out.text', var_export($result,TRUE));
    	echo $result;
    }
    
    //找回复内容
    public function content($postObj){
    	//搜索userId
    	$weixinNum=$postObj->ToUserName;
    	$this->load->model('user/userAppDb','userAppDb');
    	$userId = $this->userAppDb->search(array('weixinNum'=>$weixinNum),array())['data'][0]['userId'];
    	if (count($userId) == 0)return $this->transmitText($postObj,'找不到该微信原始ID，请联系该公众号管理员修改。');
    
    	//搜索要回复的素材
    	$this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
    	switch ($postObj->Event)
    	{
    		case "subscribe":
    			$weixinSubscribe=$this->wxSubscribeAo->search($userId,array('isRelease'=>2),'')['data'][0];//'isRelease'=>2 已发布
    			$weixinSubscribeId=$weixinSubscribe['weixinSubscribeId'];
    			break;
    		case "CLICK":
    			$weixinSubscribe=$this->wxSubscribeAo->search($userId,array('weixinSubscribeId'=>$postObj->EventKey),'')['data'][0];
    			break;
    	
    	}
    	$weixinSubscribeId=$weixinSubscribe['weixinSubscribeId'];
    	
    	//materialClassifyId要回复的类型
    	$materialClassifyId=$weixinSubscribe['materialClassifyId'];
    	$graphic=$this->wxSubscribeAo->graphicSearch($userId,$weixinSubscribeId);
    	
    	//找不到回复内容，转到客服
    	if (count($graphic) == 0)return $this->transmitService($postObj);
    	
    	//file_put_contents(dirname(__FILE__).'/out.text', var_export($graphic,TRUE));
    	//print_r($graphic);die();

    	switch ($materialClassifyId){
    		//多图文
    		case 1:
    			return $this->transmitNews($postObj,$graphic);
    			break;
    		//单图文
    		case 2:
    			return $this->transmitNews($postObj,$graphic);
    			break;
    		//单图文
    		case 3:
    			return $this->transmitText($postObj,$graphic[0]['Description']);
    			break;
    				 
    	}
    	
    }
    
    
    //接收事件消息
    public function receiveEvent($object)
    {
        switch ($object->Event)
        {
            case "subscribe":
            	return $this->content($object);
                break;
            case "CLICK":
            	return $this->content($object);
                break;
            
        }
        
       //print_r($result);
    }

    //接收文本消息
    public function receiveText($object)
    {
        $keyword = trim($object->Content);
        //多客服人工回复模式
            $result = $this->transmitService($object);
        return $result;
    }

    //接收图片消息
    public function receiveImage($object)
    {
        $content = array("MediaId"=>$object->MediaId);
        $result = $this->transmitImage($object, $content);
        return $result;
    }

    //接收位置消息
    public function receiveLocation($object)
    {
        $content = "你发送的是位置，纬度为：".$object->Location_X."；经度为：".$object->Location_Y."；缩放级别为：".$object->Scale."；位置为：".$object->Label;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //接收语音消息
    public function receiveVoice($object)
    {
        if (isset($object->Recognition) && !empty($object->Recognition)){
            $content = "你刚才说的是：".$object->Recognition;
            $result = $this->transmitText($object, $content);
        }else{
            $content = array("MediaId"=>$object->MediaId);
            $result = $this->transmitVoice($object, $content);
        }

        return $result;
    }

    //接收视频消息
    public function receiveVideo($object)
    {
        $content = array("MediaId"=>$object->MediaId, "ThumbMediaId"=>$object->ThumbMediaId, "Title"=>"", "Description"=>"");
        $result = $this->transmitVideo($object, $content);
        return $result;
    }

    //接收链接消息
    public function receiveLink($object)
    {
        $content = "你发送的是链接，标题为：".$object->Title."；内容为：".$object->Description."；链接地址为：".$object->Url;
        $result = $this->transmitText($object, $content);
        return $result;
    }

    //回复文本消息
    public function transmitText($object, $content)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[text]]></MsgType>
<Content><![CDATA[%s]]></Content>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), $content);
        return $result;
    }

    //回复图片消息
    public function transmitImage($object, $imageArray)
    {
        $itemTpl = "<Image>
    <MediaId><![CDATA[%s]]></MediaId>
</Image>";

        $item_str = sprintf($itemTpl, $imageArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[image]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复语音消息
    public function transmitVoice($object, $voiceArray)
    {
        $itemTpl = "<Voice>
    <MediaId><![CDATA[%s]]></MediaId>
</Voice>";

        $item_str = sprintf($itemTpl, $voiceArray['MediaId']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[voice]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复视频消息
    public function transmitVideo($object, $videoArray)
    {
        $itemTpl = "<Video>
    <MediaId><![CDATA[%s]]></MediaId>
    <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
</Video>";

        $item_str = sprintf($itemTpl, $videoArray['MediaId'], $videoArray['ThumbMediaId'], $videoArray['Title'], $videoArray['Description']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[video]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复图文消息
    public function transmitNews($object, $graphic)
    {
    	$content = array();
    	foreach ($graphic as $v){
    		$content[] = array("Title"=>$v['Title'], "Description"=>$v['Description'], "PicUrl"=>'http://'.$_SERVER[HTTP_HOST].$v['PicUrl'], "Url" =>$v['Url']);
    	}
    	
    	
        if(!is_array($content)){
            return;
        }
        $itemTpl = "    <item>
        <Title><![CDATA[%s]]></Title>
        <Description><![CDATA[%s]]></Description>
        <PicUrl><![CDATA[%s]]></PicUrl>
        <Url><![CDATA[%s]]></Url>
    </item>
";
        $item_str = "";
        foreach ($content as $item){
            $item_str .= sprintf($itemTpl, $item['Title'], $item['Description'], $item['PicUrl'], $item['Url']);
        }
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[news]]></MsgType>
<ArticleCount>%s</ArticleCount>
<Articles>
$item_str</Articles>
</xml>";
        
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time(), count($content));
        return $result;
    }

    //回复音乐消息
    public function transmitMusic($object, $musicArray)
    {
        $itemTpl = "<Music>
    <Title><![CDATA[%s]]></Title>
    <Description><![CDATA[%s]]></Description>
    <MusicUrl><![CDATA[%s]]></MusicUrl>
    <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
</Music>";

        $item_str = sprintf($itemTpl, $musicArray['Title'], $musicArray['Description'], $musicArray['MusicUrl'], $musicArray['HQMusicUrl']);

        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[music]]></MsgType>
$item_str
</xml>";

        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    //回复多客服消息
    public function transmitService($object)
    {
        $xmlTpl = "<xml>
<ToUserName><![CDATA[%s]]></ToUserName>
<FromUserName><![CDATA[%s]]></FromUserName>
<CreateTime>%s</CreateTime>
<MsgType><![CDATA[transfer_customer_service]]></MsgType>
</xml>";
        $result = sprintf($xmlTpl, $object->FromUserName, $object->ToUserName, time());
        return $result;
    }

    

}
?>