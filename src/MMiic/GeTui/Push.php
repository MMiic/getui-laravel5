<?php
namespace MMiic\GeTui;

use MMiic\GeTui\Exception\RequestException;
use MMiic\GeTui\Igetui\DictionaryAlertMsg;
use MMiic\GeTui\Igetui\IGtAPNPayload;
use MMiic\GeTui\Igetui\IGtAppMessage;
use MMiic\GeTui\Igetui\IGtSingleMessage;
use MMiic\GeTui\Igetui\IGtTarget;
use MMiic\GeTui\Igetui\Template\IGtLinkTemplate;
use MMiic\GeTui\Igetui\Template\IGtNotificationTemplate;
use MMiic\GeTui\Igetui\Template\IGtNotyPopLoadTemplate;
use MMiic\GeTui\Igetui\Template\IGtTransmissionTemplate;

/**
 * Created by PhpStorm.
 * User: T123
 * Date: 2016/8/25
 * Time: 14:10
 */
class Push
{
    private $HOST = 'http://sdk.open.api.igexin.com/apiex.htm';
    private $APPKEY = 'vE3LWoyS868zSSuK0DZ6k2';
    private $APPID = 'YMs6ODrNcUAp26navsUxg';
    private $APPSECRET = 'IxhiHLvSRB8QOrnFPsHfl8';
    private $MASTERSECRET = 'avBOsXTdXT9ebzSxkIjDDA';

    public function __construct()
    {

    }

    /**
     * 推送单体消息
     */
    public function pushMessageToSingle($cid, $data, $templateType = 1)
    {
        $igt = new IGtPush(NULL, $this->APPKEY, $this->MASTERSECRET, false);
        $template = $this->getTemplate($data, $templateType);

        //个推信息体
        $message = new IGtSingleMessage();

        $message->set_isOffline(true);//是否离线
        $message->set_offlineExpireTime(3600 * 12 * 1000);//离线时间
        $message->set_data($template);//设置推送消息类型
        //	$message->set_PushNetWorkType(0);//设置是否根据WIFI推送消息，1为wifi推送，0为不限制推送
        //接收方
        $target = new IGtTarget();
        $target->set_appId($this->APPID);
        $target->set_clientId($cid);
        //    $target->set_alias(Alias);


        try {
            $rep = $igt->pushMessageToSingle($message, $target);
//            var_dump($rep);
//            echo("<br><br>");
            return $rep;
        } catch (RequestException $e) {
            $requstId = e . getRequestId();
            $rep = $igt->pushMessageToSingle($message, $target, $requstId);
//            var_dump($rep);
//            echo("<br><br>");
            return $rep;
        }
    }

    /**
     * 群推接口
     */
    public function pushMessageToApp($data, $templateType = 1, $taskGroupName = null)
    {
        $igt = new IGtPush($this->HOST, $this->APPKEY, $this->MASTERSECRET);
        $template = $this->getTemplate($data, $templateType);
        //个推信息体
        //基于应用消息体
        $message = new IGtAppMessage();
        $message->set_isOffline(true);
        $message->set_offlineExpireTime(10 * 60 * 1000);//离线时间单位为毫秒，例，两个小时离线为3600*1000*2
        $message->set_data($template);

        $appIdList = array($this->APPID);
//        $phoneTypeList = array('ANDROID');
//        $provinceList = array('浙江');
//        $tagList = array('haha');
        //用户属性
        //$age = array("0000", "0010");


        //$cdt = new AppConditions();
        // $cdt->addCondition(AppConditions::PHONE_TYPE, $phoneTypeList);
        // $cdt->addCondition(AppConditions::REGION, $provinceList);
        //$cdt->addCondition(AppConditions::TAG, $tagList);
        //$cdt->addCondition("age", $age);

        $message->set_appIdList($appIdList);
        //$message->set_conditions($cdt->getCondition());

        $rep = $igt->pushMessageToApp($message, $taskGroupName);

//        var_dump($rep);
//        echo("<br><br>");
        return $rep;
    }

    public function igt()
    {
        return new IGtPush($this->HOST, $this->APPKEY, $this->MASTERSECRET);
    }

    public function batch()
    {
        return new IGtBatch($this->APPKEY, $this->igt());
    }

    /**
     * 整合消息模板
     * @param $data
     * @param int $type 1透传功能模板,2通知弹框下载模板,3通知链接模板,4通知透传模板
     */
    public function getTemplate($data, $type = 1)
    {
        switch ($type) {
            case 1:
                $template = $this->IGtTransmissionTemplate($data);
                break;
            case 2:
                $template = $this->IGtNotyPopLoadTemplate($data['notyTitle'], $data['notyContent'], $data['popTitle'], $data['popContent'], $data['loadTitle'], $data['loadUrl']);
                break;
            case 3:
                $template = $this->IGtLinkTemplate($data['title'], $data['text'], $data['url']);
                break;
            case 4:
                $template = $this->IGtNotificationTemplate($data['content'], $data['title'], $data['text']);
                break;
        }

        return $template;
    }

    /**
     * 透传功能模板
     * @return IGtTransmissionTemplate
     */
    public function IGtTransmissionTemplate($content)
    {
        $template = new IGtTransmissionTemplate();
        $template->set_appId($this->APPID);//应用appid
        $template->set_appkey($this->APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($content);//透传内容
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

        //APN高级推送
        $apn = new IGtAPNPayload();
        $alertmsg = new DictionaryAlertMsg();
        $alertmsg->body = "body";
        $alertmsg->actionLocKey = "ActionLockey";
        $alertmsg->locKey = "LocKey";
        $alertmsg->locArgs = array("locargs");
        $alertmsg->launchImage = "launchimage";
//        IOS8.2 支持
        $alertmsg->title = "Title";
        $alertmsg->titleLocKey = "TitleLocKey";
        $alertmsg->titleLocArgs = array("TitleLocArg");

        $apn->alertMsg = $alertmsg;
        $apn->badge = 7;
        $apn->sound = "";
        $apn->add_customMsg("payload", "payload");
        $apn->contentAvailable = 1;
        $apn->category = "ACTIONABLE";
        $template->set_apnInfo($apn);

        return $template;
    }

    /**
     * 通知弹框下载模板
     * @param $notyTitle 通知栏标题
     * @param $notyContent 通知栏内容
     * @param $popTitle 弹框标题
     * @param $popContent 弹框内容
     * @param $loadTitle 下载标题
     * @param $loadUrl 下载地址
     * @return IGtNotyPopLoadTemplate
     */
    public function IGtNotyPopLoadTemplate($notyTitle, $notyContent, $popTitle, $popContent, $loadTitle, $loadUrl)
    {
        $template = new IGtNotyPopLoadTemplate();

        $template->set_appId($this->APPID);//应用appid
        $template->set_appkey($this->APPKEY);//应用appkey
        //通知栏
        $template->set_notyTitle($notyTitle);//通知栏标题
        $template->set_notyContent($notyContent);//通知栏内容
        $template->set_notyIcon("");//通知栏logo
        $template->set_isBelled(true);//是否响铃
        $template->set_isVibrationed(true);//是否震动
        $template->set_isCleared(true);//通知栏是否可清除
        //弹框
        $template->set_popTitle($popTitle);//弹框标题
        $template->set_popContent($popContent);//弹框内容
        $template->set_popImage("");//弹框图片
        $template->set_popButton1("下载");//左键
        $template->set_popButton2("取消");//右键
        //下载
        $template->set_loadIcon("");//弹框图片
        $template->set_loadTitle($loadTitle);
        $template->set_loadUrl($loadUrl);
        $template->set_isAutoInstall(false);
        $template->set_isActived(true);
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息

        return $template;
    }

    /**
     * 通知链接模板
     * @return IGtLinkTemplate
     */
    public function IGtLinkTemplate($title, $text, $url)
    {
        $template = new IGtLinkTemplate();
        $template->set_appId($this->APPID);//应用appid
        $template->set_appkey($this->APPKEY);//应用appkey
        $template->set_title($title);//通知栏标题
        $template->set_text($text);//通知栏内容
        $template->set_logo("");//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        $template->set_url($url);//打开连接地址
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //iOS推送需要设置的pushInfo字段
        $apn = new IGtAPNPayload();
//        $apn->alertMsg = $title;
        $apn->badge = 11;
        $apn->actionLocKey = "启动";
        //        $apn->category = "ACTIONABLE";
        //        $apn->contentAvailable = 1;
        $apn->locKey = $text;
        $apn->title = $title;
        $apn->titleLocArgs = array("titleLocArgs");
        $apn->titleLocKey = $title;
        $apn->body = "body";
        $apn->customMsg = array("payload" => "payload");
        $apn->launchImage = "launchImage";
        $apn->locArgs = array("locArgs");
//        $apn->sound = ("test1.wav");;
        $template->set_apnInfo($apn);
        return $template;
    }

    /**
     * 通知透传模板
     * @return IGtNotificationTemplate
     */
    function IGtNotificationTemplate($content, $title, $text, $logo = null)
    {
        $template = new IGtNotificationTemplate();
        $template->set_appId($this->APPID);//应用appid
        $template->set_appkey($this->APPKEY);//应用appkey
        $template->set_transmissionType(1);//透传消息类型
        $template->set_transmissionContent($content);//透传内容
        $template->set_title($title);//通知栏标题
        $template->set_text($text);//通知栏内容
        $logo ? $template->set_logo($logo) : '';//通知栏logo
        $template->set_isRing(true);//是否响铃
        $template->set_isVibrate(true);//是否震动
        $template->set_isClearable(true);//通知栏是否可清除
        //$template->set_duration(BEGINTIME,ENDTIME); //设置ANDROID客户端在此时间区间内展示消息
        //iOS推送需要设置的pushInfo字段
        $apn = new IGtAPNPayload();
        $apn->alertMsg = "alertMsg";
        $apn->badge = 11;
        $apn->actionLocKey = "启动";
        //        $apn->category = "ACTIONABLE";
        //        $apn->contentAvailable = 1;
        $apn->locKey = $text;
        $apn->title = $title;
        $apn->titleLocArgs = array("titleLocArgs");
        $apn->titleLocKey = $title;
        $apn->body = "body";
        $apn->customMsg = array("payload" => "payload");
        $apn->launchImage = "launchImage";
        $apn->locArgs = array("locArgs");

//        $apn->sound=("test1.wav");;
        $template->set_apnInfo($apn);
        return $template;
    }
}