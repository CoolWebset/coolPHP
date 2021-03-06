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

namespace addons\invoiceform\controller;

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
        $data['addtime'] = time();
        $data['ip'] = getIp();
        db('invoiceform')->insert($data);
        $result['status'] = 1;
        return $result;
    }
}
