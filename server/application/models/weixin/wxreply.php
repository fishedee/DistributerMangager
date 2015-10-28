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
    public function content($postObj,$EventKey=''){
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
                //检测是否开通了高级分成功能
                $this->load->model('user/userPermissionDb','userPermissionDb');
                $condition['permissionId'] = 5;
                $result = $this->userPermissionDb->checkPermissionId2($userId,$condition);
                if($result){
                    $this->load->model('distribution/distributionAo');
                    $key = $postObj->EventKey;
                    $openId = $postObj->FromUserName;
                    if(strstr($key, 'qrscene')){
                        $info = substr($key, strpos($key, '_')+1);
                        $info = explode(',', $info);
                        $vender = $info[0];
                        $upUserId = $info[1];
                        $line   = $info[2];
                        //扫描成为分销商
                        $content = $this->distributionAo->qrCodeAsk($openId,$vender,$upUserId,$line);
                        return $this->transmitText($postObj,$content);
                    }else{
                        //判断openid是否分配了账号
                        $result = $this->distributionAo->checkUserClientId2($userId,$openId);
                        return $this->transmitText($postObj,$result);
                    }
                }else{
                    $weixinSubscribe=$this->wxSubscribeAo->search($userId,array('isRelease'=>2),'')['data'][0];//'isRelease'=>2 已发布
                    $weixinSubscribeId=$weixinSubscribe['weixinSubscribeId'];
                }
                break;
            case "CLICK":
                if($postObj->EventKey == 'distribution'){
                    $weixinSubscribe = $this->wxSubscribeAo->search($userId,array('remark'=>'申请分销'),'')['data'][0];
                    $weixinSubscribeId = $weixinSubscribe['weixinSubscribeId'];
                }elseif($postObj->EventKey == 'qrcode'){
                    //先判断有无分成关系 若无分成关系 则没有分配二维码
                    $this->load->model('distribution/distributionAo');
                    $ToUserName   = $postObj->ToUserName; //开发者微信号
                    $openId = $postObj->FromUserName;
                    $result = $this->distributionAo->checkHasDistribution($ToUserName,$openId);
                    if($result){
                        //发送图文信息 我的二维码
                        $this->load->model('user/userAo','userAo');
                        $info   = $this->userAo->myQrCode($userId,$openId);
                        return $this->transmitNews($postObj,$info,$postObj->EventKey);
                    }else{
                        //无建立分成关系
                        return $this->transmitText($postObj,'您还没有建立分成关系,赶快申请成为一级代理商或者成为别人分销商吧!');
                    }
                }elseif($postObj->EventKey == 'checkin'){
                    //签到
                    $this->load->model('client/scoreAo','scoreAo');
                    $openId = $postObj->FromUserName;
                    $ToUserName = $postObj->ToUserName;
                    $result = $this->scoreAo->checkIn($ToUserName,$openId);
                    return $this->transmitText($postObj,$result);
                }elseif($postObj->EventKey == 'recommend'){
                    $this->load->model('distribution/distributionAo','distributionAo');
                    $info = $this->distributionAo->getRecommend($userId);
                    if($info == 0){
                        return $this->transmitText(1,'该厂家没有设置推荐人');
                    }else{
                        return $this->transmitNews($postObj,$info,$postObj->EventKey);
                        // return $this->transmitText($postObj,$info);
                        // return $this->transmitText($postObj,implode($info, ','));
                    }
                }elseif($postObj->EventKey == 'product' || $postObj->EventKey == 'after'){
                    return $this->transmitText($postObj,'功能内测，稍后开放！');
                }
                break;
            case 'user_get_card':
                $this->load->model('member/memberAo','memberAo');
                $this->memberAo->testAdd();
                break;
            case 'SCAN':
                if($postObj->EventKey == 'distribution'){
                    $this->load->model('distribution/distributionQrCodeAo','distributionQrCodeAo');
                    $openId = $postObj->FromUserName;
                    $content = $this->distributionQrCodeAo->qrAsk2($userId,$openId);
                    return $this->transmitText($postObj,$content);
                    break;
                }elseif(strstr($postObj->EventKey, 'board')){
                    $this->load->model('client/clientAo','clientAo');
                    $openId = $postObj->FromUserName;
                    $EventKey = $postObj->EventKey;
                    $ToUserName = $postObj->ToUserName;
                    $result = $this->clientAo->checkCache($ToUserName,$openId);
                    // return $this->transmitText($postObj,$result);die;
                    if($result == 1){
                        return $this->transmitText($postObj,'1');
                    }else{
                        $this->clientAo->scanInfo($ToUserName,$openId);
                        $weixinSubscribe = $this->wxSubscribeAo->search($userId,array('remark'=>'订餐入口'),'')['data'][0];
                        $weixinSubscribeId = $weixinSubscribe['weixinSubscribeId'];
                    }
                    break;
                }else{
                    $info = $postObj->EventKey;
                    $openId = $postObj->FromUserName;
                    $info = explode(',', $info);
                    $vender = $info[0];
                    $upUserId = $info[1];
                    $line   = $info[2];
                    //扫描成为分销商
                    $this->load->model('distribution/distributionAo');
                    $content = $this->distributionAo->qrCodeAsk($openId,$vender,$upUserId,$line);
                    return $this->transmitText($postObj,$content);
                }
                break;
        }

        $weixinSubscribeId=$weixinSubscribe['weixinSubscribeId'];
        
        //materialClassifyId要回复的类型
        $materialClassifyId=$weixinSubscribe['materialClassifyId'];
        $graphic=$this->wxSubscribeAo->graphicSearch($userId,$weixinSubscribeId);
        
        //找不到回复内容，转到客服
        if (count($graphic) == 0)return $this->transmitService($postObj);
        
        switch ($materialClassifyId){
            //多图文
            case 1:
                return $this->transmitNews($postObj,$graphic);
                break;
            //单图文
            case 2:
                return $this->transmitNews($postObj,$graphic,$EventKey);
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
            case 'user_get_card':
                //获取会员卡的code
                $ToUserName   = $object->ToUserName; //开发者微信号
                $UserCardCode = $object->UserCardCode;
                $openid = $object->FromUserName;
                $CardId = $object->CardId;
                $this->load->model('vip/vipAo','vipAo');
                $this->vipAo->addMember($UserCardCode,$openid,$ToUserName,$CardId);
                break;
            case 'SCAN':
                if($object->EventKey == 'distribution'){
                    return $this->content($object);
                }elseif(strstr($object->EventKey, 'board')){
                    return $this->content($object);
                }else{
                    return $this->content($object);
                }
                break;
            
        }
        
       //print_r($result);
    }

    //接收文本消息
    public function receiveText($object)
    {
        $this->load->model('weixin/wxSubscribeAo','wxSubscribeAo');
        $keyword = trim($object->Content);
        $keyWordInfo = $this->wxSubscribeAo->keyWordSearch($keyword);
        switch ($keyWordInfo['materialClassifyId']) {
            case 1:
                //多图文
                $graphic = $this->wxSubscribeAo->materialSearch($keyWordInfo['weixinSubscribeId']);
                return $this->transmitNews($object,$graphic);
                break;
            case 2:
                //单图文
                $graphic = $this->wxSubscribeAo->materialSearch($keyWordInfo['weixinSubscribeId']);
                return $this->transmitNews($object,$graphic);
                break;
            case 3:
                //文字
                $materialInfo = $this->wxSubscribeAo->materialSearch($keyWordInfo['weixinSubscribeId']);
                return $this->transmitText($object,$materialInfo[0]['Description']);
                break;
            default:
                //多客服人工回复模式
                return $this->transmitService($object);
                break;
        }
        //多客服人工回复模式
        //     $result = $this->transmitService($object);
        // return $result;
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
    public function transmitNews($object, $graphic,$EventKey)
    {
        $content = array();
        $weixinNum=$object->ToUserName;
        $this->load->model('user/userAppDb','userAppDb');
        $userId = $this->userAppDb->search(array('weixinNum'=>$weixinNum),array())['data'][0]['userId'];
        // foreach ($graphic as $v){
     //        $content[] = array("Title"=>$v['Title'], "Description"=>$v['Description'], "PicUrl"=>'http://'.$_SERVER[HTTP_HOST].$v['PicUrl'], "Url" =>$v['Url']);
        // }

        if(strstr($object->EventKey, 'board')){
            $EventKey = substr($object->EventKey, 5);
            $this->load->model('board/boardAo','boardAo');
            $boardId = $this->boardAo->getBoardId($userId,$EventKey);
            foreach ($graphic as $v) {
                $content[] = array("Title"=>$v['Title'],"Description"=>"点击进入第".$EventKey."号桌","PicUrl"=>'http://'.$_SERVER[HTTP_HOST].$v['PicUrl'], "Url"=>$v['Url'].'?boardId='.$boardId);
            }
        }elseif($EventKey == 'recommend'){
            foreach ($graphic as $key => $v) {
                $content[] = array("Title"=>$v['company'],"Description"=>$v['nickName'],"PicUrl"=>$v['img'], "Url"=>$v['url']);
            }
        }else{
            foreach ($graphic as $v){
                $content[] = array("Title"=>$v['Title'],"Description"=>$v['Description'], "PicUrl"=>'http://'.$_SERVER[HTTP_HOST].$v['PicUrl'], "Url" =>$v['Url']);
            }
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