<?php
namespace app\home\controller;

use think\Controller;
use think\Input;
use think\Db;
use think\Request;
use clt\Gather;
use clt\Sign;

class Weidianji extends Common
{
    public $wc;
    public function _initialize()
    {
        parent::_initialize();
        //获取微信配置信息
        $this->wc = db('wx_user')->where('id', 1)->find();
    }
    public function index()
    {
        //dump($this->wc);
        return $this->fetch();
    }
    public function dourl()
    {
        header("Content-type: text/html; charset=utf-8");
        //dump($this->wc);
        //https://kuaibao.qq.com/s/20170929A00EB800?titleFlag=2
        //http://mp.weixin.qq.com/s/Cb3M0c8N6--rmnpX7dVuAg
        $url = Request::instance()->param('url');
        $Gather = new Gather ($url, './image');
        $id = $Gather->fetch();
        if ($id) {
            $call_back['err_code'] = '118';
            $call_back['err_info'] = $id;
            echo json_encode($call_back);
            exit;
        } else {
            $call_back['err_code'] = '115';
            $call_back['err_info'] = '留言失败！';
            echo json_encode($call_back);
            exit;
        }
    }
    private function getUrlRoot($url)
    {
        #添加头部和尾巴
        $url = $url . "/";
        $arr=parse_url($url);
        return $arr['host'];
    }

   
    public function show()
    {
        //dump($this->wc);
        //echo $this->getjsapi_ticket().'//';
        $Sign = new Sign ();
        //dump($Sign);
        $signPackage = $Sign->getsign();

        //$signPackage = '';
        //dump($signPackage);
        $wid = input('param.id');
        $list = db('wdjcontent')->find($wid);
        
        //查询插件设置
        $configlist = db('plugin')->where('code="wdj"')->find();
        $config = unserialize($configlist['config_value']);
        $this->assign('config', $config);
        $this->assign('list', $list);
        $this->assign('signPackage', $signPackage);
        $UrlRoot = $this->getUrlRoot($list['url']);
        switch ($UrlRoot) {
            case 'mp.weixin.qq.com':
                return $this->fetch();
             break;
            case 'kuaibao.qq.com':
                return $this->fetch('weidianji_show_kuaibao');
             break;
            case 'wxn.qq.com':
                return $this->fetch('weidianji_show_qq');
             break;
             
            case 'view.inews.qq.com':
                return $this->fetch('weidianji_show_qq');
             break;
            default:
                return $this->fetch();
             break;
        }
    }
}
