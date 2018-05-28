<?php
namespace clt;

use clt\Gather;
use think\Db;
use think\Request;

class Sign
{
    public function __construct()
    {
        //获取微信配置信息
        $this->wc = db('wx_user')->where('id', 1)->find();
        $wclist = $this->wc;
        $this->appid = $wclist['appid'];
        $this->appsecret = $wclist['appsecret'];
        $this->expires = $wclist['web_expires'];
        $this->access_token = $wclist['web_access_token'];
    }

    public function getaccess_token()
    {
        
        //获取appid secret
        $appid = $this->appid;
        $secret = $this->appsecret;
        $expires = $this->expires;
        $min = time() - $expires;
        $access_token = $this->access_token;
        if ($min<7200) {
             return $access_token;
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt ($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret);
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
            $arr = json_decode($file_contents, true);
            $data['web_access_token'] = $arr['access_token'];
            $data['web_expires'] = time();
            $res = db('wx_user')->where('id', 1)->update($data);
            return $arr['access_token'];
        }
    }

     public function getjsapi_ticket()
    {
        $access_token = $this->getaccess_token();
        //echo 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi';
        $ch = curl_init();
        $timeout = 5;
        curl_setopt ($ch, CURLOPT_URL, 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$access_token.'&type=jsapi');
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        $arr = json_decode($file_contents, true);
        return $arr['ticket'];
    }

    private function getRandChar($length)
    {
           $str = null;
           $strPol = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz";
           $max = strlen($strPol)-1;

        for ($i=0; $i<$length; $i++) {
            $str.=$strPol[rand(0, $max)];//rand($min,$max)生成介于min和max两个数之间的一个随机整数
        }

           return $str;
    }

    public function getsign()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $nonceStr = $this->getRandChar(16);
        $timestamp = time();
        $string1 = 'jsapi_ticket='.$this->getjsapi_ticket().'&noncestr='.$nonceStr.'&timestamp='.$timestamp.'&url='.$url;
        $signature = sha1($string1);
        $signPackage = array(
        "appId"     => $this->appid,
        "nonceStr"  => $nonceStr,
        "timestamp" => $timestamp,
        "url"       => $url,
        "signature" => $signature,
        "rawString" => $string1,
        "jsapi_ticket" => $this->getjsapi_ticket(),
        );
        return $signPackage;
    }
}