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
        'title' => '自定义表单',
        'description' => 'thinkph5插件测试',
        'status' => 0,
        'author' => 'by wzs',
        'version' => '0.1'
    ];

    /**
     * 插件安装方法
     * @return bool
     */
    public function install()
    {
      $db_prefix = config('database.prefix');
          $table_name = "{$db_prefix}diyform";
          $sql=
          <<<SQL
          CREATE TABLE IF NOT EXISTS `{$table_name}` (
            `document_id` int(10) unsigned NOT NULL,
            `good` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '赞数',
            `bad` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '批数',
            `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
            `uids` longtext NOT NULL COMMENT '投过票的用户id 字符合集 id1,id2,',
            PRIMARY KEY (`document_id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
SQL;
          db()->execute($sql);
          if(count(db()->query("SHOW TABLES LIKE '{$table_name}'")) != 1){
              session('addons_install_error', ',diyform表未创建成功，请手动检查插件中的sql，修复后重新安装');
              return false;
          }
          return true;
    }

    /**
     * 插件卸载方法
     * @return bool
     */
     public function uninstall(){
        $db_prefix = config('database.prefix');
        $sql = "DROP TABLE IF EXISTS `{$db_prefix}diyform`;";
        db()->execute($sql);
        return true;
     }

    /**
     * 实现的diy_form钩子方法
     * @return mixed
     */
    public function diyformhook($param)
    {
        echo '<p><font color="red">开始处理钩子啦</font></p>';
        echo '<p><font color="green">打印传给钩子的参数：</font></p>';
        dump($param);
        echo '<p><font color="green">打印插件配置：</font></p>';
        dump($this->getConfig());

        // 这里可以通过钩子来调用钩子模板
        return $this->fetch('info');
    }

}
