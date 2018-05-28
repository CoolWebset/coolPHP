<?php
namespace app\home\controller;
use think\Controller;
use think\Input;
use think\Db;
use think\Request;
use clt\Gather;
use clt\Sign;
class Datastatistics extends Controller{
    public function _initialize(){
      $treemounth = strtotime(date("Y-m-d",strtotime("-3 month")));
      db('visit_day')->where('acctime','<',$treemounth)->delete();
    }
    public function index(){
        $screen_height =  Request::instance()->param('screen_height');
        $screen_width = Request::instance()->param('screen_width');
        $os = Request::instance()->param('screen_height');
        $browser = Request::instance()->param('browser');
        $referrer = Request::instance()->param('referrer');
        $url = Request::instance()->param('url');
        $keystr = md5($screen_height.$screen_width.$os.$browser.'coolphp');
        if(cookie('keystr') && cookie('keystr') == $keystr){

        }else{
          cookie('keystr', $keystr, 3600);
        }
        $call_back['referrer'] = $referrer;
        $call_back['url'] = $url;
        echo json_encode($call_back);
        exit;
    }
    public function dostatistics(){
      $antepage = Request::instance()->param('antepage');
      $visitpage = Request::instance()->param('visitpage');
      $this ->visit_detail($visitpage);
      $this ->visit_day($antepage,$visitpage);
    }
    protected function visit_day($antepage,$visitpage){
     //dump($_SERVER);
     $data['antepage'] = $antepage;
     $data['visitpage'] = $visitpage;
     $data['browser'] = GetBrowser();
     $data['ip'] = Getcip();
     $data['acctime'] = time();
     if(cookie('keystr')){
       $data['keystr'] = cookie('keystr');
     }
     $res = db('visit_day')->insert($data);
    }
    protected function visit_detail($visitpage){
      $data['name'] = $visitpage;
      //echo $data['name'];exit;
      $data['stattime'] = strtotime(date("Y-m-d"),time());
      //处理pv
      $pvlist = db('visit_detail')->where('name ="'.$data['name'].'" and stattime = '.$data['stattime'])->find();
      if($pvlist){
        //今天该页面有人访问过，则更新数据
        $iplist = db('visit_day')->where('visitpage ="'.$data['name'].'"  and acctime >= '.$data['stattime'].' and ip ="'.Getcip().'"')->select();
        $data['pv'] = $pvlist['pv'] + 1;
        if($iplist){
          //当前ip今天打开过该页面
          $data['ip'] = $pvlist['ip'];
        }else{
          //当前ip今天没打开过该页面
          $data['ip'] = $pvlist['ip'] + 1;
        }

        //判断真实用户
        if(cookie('keystr')){
          $uvlist = db('visit_day')->where('visitpage ="'.$data['name'].'"  and acctime >= '.$data['stattime'].' and ip = "'.Getcip().'" and keystr  = "'.cookie('keystr').'"')->fetchSql(false)->select();
          //dump($uvlist);
          if($uvlist){

          }else{
            $data['uv'] = $pvlist['uv'] + 1;
          }
        }
        //dump($data);
        //exit;
        unset($data['name']);unset($data['stattime']);
        $res = db('visit_detail')->where('id = '.$pvlist['id'])->fetchSql(false)->update($data);
      }else{
        //今天该页面没有人访问过，则添加数据
        $data['pv'] = 1;
        $data['uv'] = 1;
        $data['ip'] = 1;
        $res = db('visit_detail')->insert($data);
      }

    }
}
