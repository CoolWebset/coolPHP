<?php
namespace app\admin\controller;
use think\Db;
use think\Request;
use think\Controller;
class Link extends Common
{
    public function index(){
        if(request()->isPost()) {
            $val=input('post.val');
            $map = '';
            if (!empty($val)){
                $map['name|url'] = array('like',"%".$val."%");
            }
            $link=db('link')->where($map)->order('sort')->select();
            foreach ($link as $k=>$v){
                $link[$k]['addtime'] = date('Y-m-d H:s',$v['addtime']);
            }
            return $result = ['code'=>0,'msg'=>'获取成功!','data'=>$link,'rel'=>1];
        }
        return $this->fetch();
    }
    //修改友情链接状态
    public function linkState(){
        $id=input('post.id');
        $open=db('link')->where(array('link_id'=>$id))->value('open');//判断当前状态情况
        if($open==1){
            $data['open'] = 0;
            db('link')->where(array('link_id'=>$id))->setField($data);
            $data['status'] = 1;
        }else{
            $data['open'] = 1;
            db('link')->where(array('link_id'=>$id))->setField($data);
            $data['status'] = 1;
        }
        cache('linkList', NULL);
        return $data;
    }
    public function listOrder(){
        $data = input('post.');
        if(db('link')->update($data)!==false){
            cache('linkList', NULL);
            return $result = ['msg' => '操作成功！','url'=>url('index'), 'code' => 1];
        }else{
            return $result = ['code'=>0,'msg'=>'操作失败！'];
        }
    }
    //添加
    public function add(){
        if(request()->isPost()){
            $data = input('post.');
            $data['addtime'] = time();
            db('link')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '友情链接添加成功!';
            $result['url'] = url('index');
            cache('linkList', NULL);
            return $result;
        }else{
            $this->assign('title',lang('add').lang('link'));
            $this->assign('info','null');
            return $this->fetch('form');
        }
    }
    //修改友情链接
    public function edit(){
        if(request()->isPost()){
            $data = input('post.');
            db('link')->update($data);
            $result['code'] = 1;
            $result['msg'] = '友情链接修改成功!';
            $result['url'] = url('index');
            cache('linkList', NULL);
            return $result;
        }else{
            $link_id=input('param.link_id');
            $info=db('link')->where(array('link_id'=>$link_id))->find();
            $this->assign('info',json_encode($info,true));
            $this->assign('title',lang('edit').lang('link'));
            return $this->fetch('form');
        }
    }
    public function del(){
        db('link')->where(array('link_id'=>input('link_id')))->delete();
        cache('linkList', NULL);
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    public function delall(){
        $map['link_id'] =array('in',input('param.ids/a'));
        db('link')->where($map)->delete();

        $result['msg'] = '删除成功！';
        $result['code'] = 1;
        $result['url'] = url('index');
        cache('linkList', NULL);
        return $result;
    }
}