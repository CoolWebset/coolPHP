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
namespace addons\diyform;

use think\Addons;

/**
 * 插件测试
 * @author byron sampson
 */
class Diyform extends Addons
{
    public $info = [
        'name' => 'diyform',
        'title' => '预订系统',
        'description' => '客房预订,会议预订管理',
        'status' => 0,
        'author' => 'by wzs',
        'version' => '0.1',
        'is_weixin'=>1
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
      $this->dosqlfile(ADDONS_PATH.'diyform/install.sql');
      return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
     public function uninstall(){
        $this->dosqlfile(ADDONS_PATH.'diyform/uninstall.sql');
        return true;
     }

    /**
     * 实现的diy_form钩子方法
     * @return mixed
     */
    public function diyformhook($param)
    {
        // echo '<p><font color="red">开始处理钩子啦</font></p>';
        // echo '<p><font color="green">打印传给钩子的参数：</font></p>';
        // dump($param);
        // echo '<p><font color="green">打印插件配置：</font></p>';
        // dump($this->getConfig());

        // 这里可以通过钩子来调用钩子模板
        return $this->fetch('info');
    }

    private function dosqlfile($file)
    {
      $_sql = file_get_contents($file);
      $_arr = explode(';', $_sql);
      unset($_arr[count($_arr)-1]);
      // dump($_arr);exit;
      foreach ($_arr as $_value) {
        db()->query($_value.';');
      }
    }


}
