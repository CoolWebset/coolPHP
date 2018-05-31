<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
class Addons extends Common
{
    // private $addonsListM;
    public function _initialize()
    {
        parent::_initialize();
        $this->addonsListM = db('addons')->field('name')->select();

    }
    /**
     * 插件列表
     */
    public function index()
    {
        if(request()->isPost()){
          $page =input('page')?input('page'):1;
          $pageSize =input('limit')?input('limit'):config('pageSize');
          $list = $this -> getDir(ADDONS_PATH);
          $count = count($list);
          // dump($list);
          foreach ($list as $key => $value) {
            $class          =   get_addon_class($value);
            if(!class_exists($class)){
              $result['msg'] = '插件不存在!';
              $result['code'] = 0;
              return $result;
            }
            $addons  =   new $class;
            $addonsJson = $addons->info;
            $list[$key]=$addonsJson;
          }

          foreach ($list as $key => $value)
          {
            $list[$key]['id'] = $key + 1;
            // echo $value['author']['name'];
            if($this -> deep_in_array($value['name'], $this->addonsListM))
            {
              $list[$key]['status'] = 1;
            }
            else
            {
              $list[$key]['status'] = 0;
            }
          }

          $list = $this -> page_array($pageSize,$page,$list,0);
          // dump($list);
          $rsult['code'] = 0;
          $rsult['msg'] = "获取成功";
          $rsult['data'] = $list;
          $rsult['count'] = $count;
          $rsult['rel'] = 1;
          return $rsult;
        }
      return $this->fetch();

      // dump($addonsJson);
    }

    /**
     * 插件安装
     * @return [type] [description]
     */
    public function install()
    {
      $addonsName = Input('addonsname');
      $class          =   get_addon_class($addonsName);
      if(!class_exists($class)){
        $result['msg'] = '插件不存在';
        $result['code'] = 0;
        return $result;
        exit;
      }
      $addons  =   new $class;
      $info = $addons->info;
      if(!$info || !$addons->checkInfo()){
        $result['msg'] = '插件信息缺失';
        $result['code'] = 0;
        return $result;
        exit;
      }
      session('addons_install_error',null);
      $install_flag   =   $addons->install();
      if(!$install_flag){
          $result['msg'] = '执行插件预安装操作失败'.session('addons_install_error');
          $result['code'] = 0;
          return $result;
          exit;
      }
      $info['status'] = 1;
      $info['create_time'] = time();
      $res = db('addons')->insert($info);
      $config['config']=json_encode($addons->getConfig());
      $updateres = db('addons')->where('name', $addonsName)->update($config);
      if($res){
        if($updateres){
          $result['msg'] = '插件安装成功!';
          $result['url'] = url('index');
          $result['code'] = 1;
          return $result;
          exit;
        }else{
          $result['msg'] = '插件安装失败!';
          $result['code'] = 0;
          return $result;
          exit;
        }
      }else{
          $result['msg'] = '插件安装失败!';
          $result['code'] = 0;
          return $result;
          exit;
      }
    }
    public function getqr()
    {
      // $url = Input('url');
      // $name = rand(10,100)
      // define('imgurl', APP_PATH."../public/uploads/qrcode_".$name.".png");
      // \PHPQRCode\QRcode::png($url,imgurl, 'L', 4, 2);
      // $result['msg'] = '插件安装成功!';
      // $result['url'] = $name;
      // $result['code'] = 1;
      // return $result;
      // exit;
    }
    /**
     * 卸载程序
     */
    public function uninstall()
    {
      $addonsName = Input('addonsname');
      $class      =   get_addon_class($addonsName);
      $db_addons = db('addons')->where('name',$addonsName)->find();
      if(!$db_addons || !class_exists($class)){
        $result['msg'] = '插件不存在!';
        $result['code'] = 0;
        return $result;
        exit;
      }
      session('addons_uninstall_error',null);
      $addons =   new $class;
      $uninstall_flag =   $addons->uninstall();
      if(!$uninstall_flag){
        $result['msg'] = '执行插件预卸载操作失败'.session('addons_uninstall_error');
        $result['code'] = 0;
        return $result;
        exit;
      }
      $hooks_update   =  db('addons')->where('name',$addonsName)->delete();;
      if($hooks_update === false){
        $result['msg'] = '插件卸载失败!';
        $result['code'] = 0;
        return $result;
        exit;
      }else{
        $result['msg'] = '插件卸载成功!';
        $result['url'] = url('index');
        $result['code'] = 1;
        return $result;
        exit;
      }
    }
    private function getAddonsInfo($addonsName)
    {
      $json_string = file_get_contents(ADDONS_PATH .$addonsName. '/addons.json');
      // 把JSON字符串转成PHP数组 得到插件列表
      $addonsJson = json_decode($json_string, true);
      return $addonsJson;
    }

    private function getDir($dir)
    {
        $dirArray[]=NULL;
        if (false != ($handle = opendir ( $dir ))) {
            $i=0;
            while ( false !== ($file = readdir ( $handle )) ) {
                if ($file != "." && $file != ".."&&!strpos($file,".")) {
                    $dirArray[$i]=$file;
                    $i++;
                }
            }
            //关闭句柄
            closedir ( $handle );
        }
        return $dirArray;
    }

    /**
     * 插件数组分页类
     * @param  [type] $count [每页多少条数据]
     * @param  [type] $page  [当前第几页]
     * @param  [type] $array [查询出来的所有数组]
     * @param  [type] $order [0 - 不变   1- 反序]
     * @return [type] $array [返回数据]
     */
    private function page_array($count,$page,$array,$order)
    {
      global $countpage; #定全局变量
      $page=(empty($page))?'1':$page; #判断当前页面是否为空 如果为空就表示为第一页面
        $start=($page-1)*$count; #计算每次分页的开始位置
      if($order==1){
        $array=array_reverse($array);
      }
      $totals=count($array);
      $countpage=ceil($totals/$count); #计算总页面数
      $pagedata=array();
      $pagedata=array_slice($array,$start,$count);
      return $pagedata; #返回查询数据
    }

    private function deep_in_array($value, $array) {
        foreach($array as $item) {
            if(!is_array($item)) {
                if ($item == $value) {
                    return true;
                } else {
                    continue;
                }
            }

            if(in_array($value, $item)) {
                return true;
            } else if($this -> deep_in_array($value, $item)) {
                return true;
            }
        }
        return false;
    }

}
