<?php
namespace app\home\controller;
class Index extends Common{
    public function _initialize(){
        parent::_initialize();
    }
    public function index(){
        return $this->fetch();
    }
    public function book()
    {
      return $this->fetch();
    }
    public function vip()
    {
      return $this->fetch();
    }
    public function activity()
    {
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
        db('message')->insert($data);
        $result['status'] = 1;
        return $result;
    }

}
