<?php
namespace Miluo\GetuiSDK;

use IGeTui;
use IGtBatch;
use IGtAppMessage;
use IGtAPNPayload;
use IGtBaseTemplate;


class Getui{
    
    protected $app_key = '';
    protected $app_id = '';
    protected $master_secret = '';
    protected $host = '';
    protected $cid = '';
    protected $device_token = '';
    protected $alias = '';

    public function __construct($app_key='',$app_id='',$master_secret='http://sdk.open.api.igexin.com/apiex.htm',$host='',$cid='',$device_token='',$alias=''){
    	$this->app_key = $app_key;
    	$this->app_id = $app_id;
    	$this->master_secret = $master_secret;
    	$this->host = $host;
    	$this->cid = $cid;
    	$this->device_token = $device_token;
    	$this->alias = $alias;
    }
        //
        //服务端推送接口，支持三个接口推送
        //1.PushMessageToSingle接口：支持对单个用户进行推送
        //2.PushMessageToList接口：支持对多个用户进行推送，建议为50个用户
        //3.pushMessageToApp接口：对单个应用下的所有用户进行推送，可根据省份，标签，机型过滤推送
        //

    //单推接口案例
    function pushMessageToSingle(){
        	$igt = new IGeTui($this->host,$this->app_key,$this->master_secret);

    //     消息模版：
    //     1.TransmissionTemplate:透传功能模板
    //     2.LinkTemplate:通知打开链接功能模板
    //     3.NotificationTemplate：通知透传功能模板
    //     4.NotyPopLoadTemplate：通知弹框下载功能模板

    //    	$template = IGtNotyPopLoadTemplateDemo();
    //    	$template = IGtLinkTemplateDemo();
    //    	$template = IGtNotificationTemplateDemo();
        	$template = IGtTransmissionTemplateDemo();

        //个推信息体
    	$message = new IGtSingleMessage();

    	$message->set_isOffline(true);//是否离线
    	$message->set_offlineExpireTime(3600*12*1000);//离线时间
    	$message->set_data($template);//设置推送消息类型
    //	$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    	//接收方
    	$target = new IGtTarget();
    	$target->set_appId($this->app_id);
    	$target->set_clientId($this->cid);
    //    $target->set_alias(Alias);


        try {
            $rep = $igt->pushMessageToSingle($message, $target);
            var_dump($rep);
            echo ("<br><br>");

        }catch(RequestException $e){
            $requstId =e.getRequestId();
            $rep = $igt->pushMessageToSingle($message, $target,$requstId);
            var_dump($rep);
            echo ("<br><br>");
        }

    }

    function pushMessageToSingleBatch()
    {
        putenv("gexin_pushSingleBatch_needAsync=false");

        $igt = new IGeTui($this->host, $this->app_key, $this->master_secret);
        $batch = new IGtBatch($this->app_key, $igt);
        $batch->setApiUrl($this->host);
        //$igt->connect();
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板

    //    $template = IGtNotyPopLoadTemplateDemo();
        $template = IGtLinkTemplateDemo();
        //$template = IGtNotificationTemplateDemo();
    //    $template = IGtTransmissionTemplateDemo();

        //个推信息体
        $message = new IGtSingleMessage();
        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(12 * 1000 * 3600);//离线时间
        $message->set_data($template);//设置推送消息类型
    //    $message->set_PushNetWorkType(1);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送

        $target = new IGtTarget();
        $target->set_appId($this->app_id);
        $target->set_clientId($this->cid);
        $batch->add($message, $target);
        try {

            $rep = $batch->submit();
            var_dump($rep);
            echo("<br><br>");
        }catch(Exception $e){
            $rep=$batch->retry();
            var_dump($rep);
            echo ("<br><br>");
        }
    }

    //多推接口案例
    function pushMessageToList()
    {
        putenv("gexin_pushList_needDetails=true");
        putenv("gexin_pushList_needAsync=true");

        $igt = new IGeTui($this->host, $this->app_key, $this->master_secret);
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板


        //$template = IGtNotyPopLoadTemplateDemo();
        //$template = IGtLinkTemplateDemo();
        //$template = IGtNotificationTemplateDemo();
        $template = IGtTransmissionTemplateDemo();
        //个推信息体
        $message = new IGtListMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
    //    $message->set_PushNetWorkType(1);	//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    //    $contentId = $igt->getContentId($message);
        $contentId = $igt->getContentId($message,"toList任务别名功能");	//根据TaskId设置组名，支持下划线，中文，英文，数字

        //接收方1
        $target1 = new IGtTarget();
        $target1->set_appId($this->app_id);
        $target1->set_clientId($this->cid);
    //    $target1->set_alias(Alias);

        $targetList[] = $target1;

        $rep = $igt->pushMessageToList($contentId, $targetList);

        var_dump($rep);

        echo ("<br><br>");

    }

    //群推接口案例
    function pushMessageToApp(){
    	$igt = new IGeTui($this->host,$this->app_key,$this->master_secret);
        //消息模版：
        // 1.TransmissionTemplate:透传功能模板
        // 2.LinkTemplate:通知打开链接功能模板
        // 3.NotificationTemplate：通知透传功能模板
        // 4.NotyPopLoadTemplate：通知弹框下载功能模板
    	
        	//$template = IGtNotyPopLoadTemplateDemo();
        	$template = IGtLinkTemplateDemo();
        	//$template = IGtNotificationTemplateDemo();
    //    	$template = IGtTransmissionTemplateDemo();
    	
    	//个推信息体
    	//基于应用消息体
    	$message = new IGtAppMessage();
    
    	$message->set_isOffline(true);
    	$message->set_offlineExpireTime(3600*12*1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
    	$message->set_data($template);
    //	$message->set_PushNetWorkType(1);	//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
    //    $message->set_speed(50);          //控速推送，设置每秒消息的下发量
     
    	$message->set_appIdList(array($this->app_id));
    	//$message->set_phoneTypeList(array('ANDROID'));
    	//$message->set_provinceList(array('浙江','北京','河南'));
    //	$message->set_tagList(array('中文'));
    
    	$rep = $igt->pushMessageToApp($message,'toApp任务别名');//根据TaskId设置组名，支持下划线，中文，英文，数字
    
    
    	var_dump($rep);
        echo ("<br><br>");
    }
    
        	//所有推送接口均支持四个消息模板，依次为通知弹框下载模板，通知链接模板，通知透传模板，透传模板
        	//注：IOS离线推送需通过APN进行转发，需填写pushInfo字段，目前仅不支持通知弹框下载功能
    
    function IGtNotyPopLoadTemplateDemo(){
            $template =  new IGtNotyPopLoadTemplate();
    
            $template ->set_appId($this->app_id);//应用appid
            $template ->set_appkey($this->app_key);//应用appkey
            //通知栏
            $template ->set_notyTitle("个推");//通知栏标题
            $template ->set_notyContent("个推最新版点击下载");//通知栏内容
            $template ->set_notyIcon("");//通知栏logo
            $template ->set_isBelled(true);//是否响铃
            $template ->set_isVibrationed(true);//是否震动
            $template ->set_isCleared(true);//通知栏是否可清除
            //弹框
            $template ->set_popTitle("弹框标题");//弹框标题
            $template ->set_popContent("弹框内容");//弹框内容
            $template ->set_popImage("");//弹框图片
            $template ->set_popButton1("下载");//左键
            $template ->set_popButton2("取消");//右键
            //下载
            $template ->set_loadIcon("");//弹框图片
            $template ->set_loadTitle("地震速报下载");
            $template ->set_loadUrl("http://dizhensubao.igexin.com/dl/com.ceic.apk");
            $template ->set_isAutoInstall(false);
            $template ->set_isActived(true);
            //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
    
            return $template;
    }
    
    function IGtLinkTemplateDemo(){
            $template =  new IGtLinkTemplate();
            $template ->set_appId($this->app_id);//应用appid
            $template ->set_appkey($this->app_key);//应用appkey
            $template ->set_title("请输入通知标题");//通知栏标题
            $template ->set_text("请输入通知内容");//通知栏内容
            $template ->set_logo("");//通知栏logo
            $template ->set_isRing(true);//是否响铃
            $template ->set_isVibrate(true);//是否震动
            $template ->set_isClearable(true);//通知栏是否可清除
            $template ->set_url("http://www.igetui.com/");//打开连接地址
            //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //iOS推送需要设置的pushInfo字段
    //        $apn = new IGtAPNPayload();
    //        $apn->alertMsg = "alertMsg";
    //        $apn->badge = 11;
    //        $apn->actionLocKey = "启动";
    //    //        $apn->category = "ACTIONABLE";
    //    //        $apn->contentAvailable = 1;
    //        $apn->locKey = "通知栏内容";
    //        $apn->title = "通知栏标题";
    //        $apn->titleLocArgs = array("titleLocArgs");
    //        $apn->titleLocKey = "通知栏标题";
    //        $apn->body = "body";
    //        $apn->customMsg = array("payload"=>"payload");
    //        $apn->launchImage = "launchImage";
    //        $apn->locArgs = array("locArgs");
    //
    //        $apn->sound=("test1.wav");;
    //        $template->set_apnInfo($apn);
    	return $template;
    }
    
    function IGtNotificationTemplateDemo(){
            $template =  new IGtNotificationTemplate();
            $template->set_appId($this->app_id);//应用appid
            $template->set_appkey($this->app_key);//应用appkey
            $template->set_transmissionType(1);//透传消息类型
            $template->set_transmissionContent("测试离线");//透传内容
            $template->set_title("个推");//通知栏标题
            $template->set_text("个推最新版点击下载");//通知栏内容
            $template->set_logo("http://wwww.igetui.com/logo.png");//通知栏logo
            $template->set_isRing(true);//是否响铃
            $template->set_isVibrate(true);//是否震动
            $template->set_isClearable(true);//通知栏是否可清除
            //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //iOS推送需要设置的pushInfo字段
    //        $apn = new IGtAPNPayload();
    //        $apn->alertMsg = "alertMsg";
    //        $apn->badge = 11;
    //        $apn->actionLocKey = "启动";
    //    //        $apn->category = "ACTIONABLE";
    //    //        $apn->contentAvailable = 1;
    //        $apn->locKey = "通知栏内容";
    //        $apn->title = "通知栏标题";
    //        $apn->titleLocArgs = array("titleLocArgs");
    //        $apn->titleLocKey = "通知栏标题";
    //        $apn->body = "body";
    //        $apn->customMsg = array("payload"=>"payload");
    //        $apn->launchImage = "launchImage";
    //        $apn->locArgs = array("locArgs");
    //
    //        $apn->sound=("test1.wav");;
    //        $template->set_apnInfo($apn);
            return $template;
    }
    
    function IGtTransmissionTemplateDemo(){
            $template =  new IGtTransmissionTemplate();
            $template->set_appId($this->app_id);//应用appid
            $template->set_appkey($this->app_key);//应用appkey
            $template->set_transmissionType(1);//透传消息类型
            $template->set_transmissionContent("测试离线ddd");//透传内容
            //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //APN简单推送
    //        $template = new IGtAPNTemplate();
    //        $apn = new IGtAPNPayload();
    //        $alertmsg=new SimpleAlertMsg();
    //        $alertmsg->alertMsg="";
    //        $apn->alertMsg=$alertmsg;
    ////        $apn->badge=2;
    ////        $apn->sound="";
    //        $apn->add_customMsg("payload","payload");
    //        $apn->contentAvailable=1;
    //        $apn->category="ACTIONABLE";
    //        $template->set_apnInfo($apn);
    //        $message = new IGtSingleMessage();
    
        //APN高级推送
            $apn = new IGtAPNPayload();
            $alertmsg=new DictionaryAlertMsg();
            $alertmsg->body="body";
            $alertmsg->actionLocKey="ActionLockey";
            $alertmsg->locKey="LocKey";
            $alertmsg->locArgs=array("locargs");
            $alertmsg->launchImage="launchimage";
    //        IOS8.2 支持
            $alertmsg->title="Title";
            $alertmsg->titleLocKey="TitleLocKey";
            $alertmsg->titleLocArgs=array("TitleLocArg");
    
            $apn->alertMsg=$alertmsg;
            $apn->badge=7;
            $apn->sound="";
            $apn->add_customMsg("payload","payload");
            $apn->contentAvailable=1;
            $apn->category="ACTIONABLE";
            $template->set_apnInfo($apn);
    
        //PushApn老方式传参
    //    $template = new IGtAPNTemplate();
    //          $template->set_pushInfo("", 10, "", "com.gexin.ios.silence", "", "", "", "");
    
    	return $template;
    }
}

 
?>
