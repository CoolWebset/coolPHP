<?php
// +----------------------------------------------------------------------
// | thinkphp5 Addons [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2016 http://www.zzstudio.net All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Byron Sampson <xiaobo.sun@qq.com>
// +----------------------------------------------------------------------

namespace addons\temp\controller;
use think\Db;
use think\Request;
use think\addons\Controller;

class Index extends Controller
{
    public function _initialize()
    {
      //判断插件是否安装
      $this->addonsId = db('addons')->where('name',$this->addon)->find();
      if(!$this->addonsId)
      {
        $this->redirect('index/index');
      }
    }
    public function index()
    {
        $addonName = $this->addon;
        dump($this->addon);
        echo '<p>我是temp插件中Index控制器的index方法</p>';
        return $this->fetch();
    }
}
