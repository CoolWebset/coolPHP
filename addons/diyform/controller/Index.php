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

namespace addons\diyform\controller;

use think\addons\Controller;

class Index extends Controller
{
    public function index()
    {
        // echo '<p>我是temp2插件中Index控制器的index方法</p>';
        return $this->fetch();
    }
    public function meet()
    {
        // echo '<p>我是temp2插件中Index控制器的index方法</p>';
        return $this->fetch();
    }
    public function sendbook(){
        $data = input('post.');
        if($data['intime']){
          $data['intime'] = strtotime($data['intime']);
        }
        if($data['outtime']){
          $data['outtime'] = strtotime($data['outtime']);
        }
        if($data['partytime']){
          $data['partytime'] = strtotime($data['partytime']);
        }
        $data['addtime'] = time();
        $data['ip'] = getIp();
        db('diyform')->insert($data);
        $result['status'] = 1;
        return $result;
    }
}
