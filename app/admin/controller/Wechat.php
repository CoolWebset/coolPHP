<?php
namespace app\admin\controller;
use think\Db;
use clt\Leftnav;
class Wechat extends Common{
	public function index(){
		$info = db('wx_user')->where('id',1)->find();
		$this->assign('info',$info);
		$apiurl = 'http://'.$_SERVER['HTTP_HOST'].'/home/weixin/index';
		$this->assign('apiurl',$apiurl);
		return $this->fetch();
	}
	public function weixin(){
		$data = input('post.');
		db('wx_user')->where('id',1)->update($data);
		$result['code'] = 1;
		$result['msg'] = '微信公众号设置修改成功!';
		return $result;
	}
	public function menu(){
        if(request()->isPost()) {
            $wx_menu = db('wx_menu');
            $Leftnav = new Leftnav ();
            $wxMenu = $wx_menu->order('listorder')->select();
            $arr = $Leftnav::menu($wxMenu);
            return ['code'=>0,'msg'=>'获取成功！','data'=>$arr];
        }
		return $this->fetch();
	}
    //添加菜单
    public function addMenu(){
        if(request()->isPost()) {
            $data=input('post.');
            $pid = explode(':',$data['pid']);
            $data['pid'] =$pid[1]?$pid[1]:0;
            $pcount = db('wx_menu')->where('pid',0)->count();
            if($pcount>=3 and $data['pid']==0){
                $result['msg'] = '一级菜单最多三个';
                $result['code'] = 0;
                return $result;
            }
            db('wx_menu')->insert($data);
            $result['msg'] = '自定义菜单添加成功';
            $result['code'] = 1;
            $result['url'] = url('menu');
            return $result;
        }else{
            $wx_menu = db('wx_menu');
            $wxTopMenu=$wx_menu->where('pid',0)->order('listorder')->select();
            $this->assign('wxTopMenu',json_encode($wxTopMenu,true));
            $this->assign('title',lang('add').'菜单');
            $this->assign('info','null');
            return $this->fetch('menuForm');
        }
    }

    //修改自定义菜单显示
    public function editMenu(){
        if(request()->isPost()) {
            $data= input('post.');
            $pid = explode(':',$data['pid']);
            $data['pid'] =$pid[1]?$pid[1]:0;
            db('wx_menu')->update($data);
            $result['msg'] = '自定义菜单修改成功';
            $result['code'] = 1;
            $result['url'] = url('menu');
            return $result;
        }else{
            $wx_menu = db('wx_menu');
            $wxTopMenu=$wx_menu->where('pid',0)->order('listorder')->select();
            $this->assign('wxTopMenu',json_encode($wxTopMenu,true));
            $info=db('wx_menu')->where(array('id'=>input('id')))->find();
            $this->assign('info',json_encode($info,true));
            $this->assign('type','edit');
            $this->assign('title',lang('edit').'菜单');
            return $this->fetch('menuForm');
        }

    }
	//自定义菜单排序
	public function menuOrder(){
		$wx_menu=db('wx_menu');
		$data = input('post.');
        $wx_menu->update($data);
		return ['code'=>1,'msg'=>'排序更新成功！'];
	}
	//自定义菜单状态修改
    public function menuState(){
        $id=input('id');
        $wxMenu = db('wx_menu');
        $statusone=$wxMenu->where('id',$id)->value('open');//判断当前状态情况
        if($statusone==1){
            $data['open'] = 0;
        }else{
            $data['open'] = 1;
        }
        $wxMenu->where(array('id'=>$id))->update($data);
        $data['code'] = 1;
        return $data;
    }
    //删除菜单
    public function delMenu(){
        db('wx_menu')->where(array('id'=>input('id')))->delete();
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    //生成菜单
    public function createMenu(){
        $wx=db('wx_user')->where(array('id'=>1))->find();//读取微信配置参数
        $access_token = $this->get_access_token($wx['appid'],$wx['appsecret']);
        //获取父级菜单
        $p_menus = db('wx_menu')->where(array('pid'=>0))->order('id ASC')->select();
        $p_menus = convert_arr_key($p_menus,'id');
        $post_str = $this->convert_menu($p_menus);
        // http post请求
        if(!count($p_menus) > 0){
            $back['info'] = "没有菜单可发布";
            $back['code'] =0;
            return $back;
        }
        $url ="https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$access_token}";
        $return = httpRequest($url,'POST',$post_str);
        $return = json_decode($return,1);
        if($return['errcode'] == 0){
            $back['info'] = "菜单已成功生成";
            $back['code'] =1;
        }else{
            $back['info'] = "错误代码;".$return['errcode'];
            $back['code'] =0;
        }
        return $back;
    }
    //获取微信access_token
    public function get_access_token($appid,$appsecret){
        //判断是否过了缓存期
        $wxUser = db('wx_user');
        $wechat = $wxUser->find();
        $expire_time = $wechat['web_expires'];
        if($expire_time > time()){
            return $wechat['web_access_token'];
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $return = httpRequest($url,'GET');
        $return = json_decode($return,1);
        $web_expires = time() + 7000; // 提前200秒过期
        $wxUser->where(array('id'=>$wechat['id']))->update(array('web_access_token'=>$return['access_token'],'web_expires'=>$web_expires));
        return $return['access_token'];
    }
    //菜单转换
    private function convert_menu($p_menus){
        $new_arr = array();
        $count = 0;
        $wxMenu = db('wx_menu');
        foreach($p_menus as $k => $v){
            $new_arr[$count]['name'] = $v['name'];
            //获取子菜单
            $c_menus = $wxMenu->where(array('pid'=>$k))->select();

            if($c_menus){
                foreach($c_menus as $kk=>$vv){
                    $add = array();
                    $add['name'] = $vv['name'];
                    $add['type'] = $vv['type'];
                    // click类型
                    if($add['type'] == 'click'){
                        $add['key'] = $vv['value'];
                    }elseif($add['type'] == 'view'){
                        $add['url'] = $vv['value'];
                    }else{
                        $add['key'] = $vv['value'];
                    }
                    $add['sub_button'] = array();
                    if($add['name']){
                        $new_arr[$count]['sub_button'][] = $add;
                    }
                }
            }else{
                $new_arr[$count]['type'] = $v['type'];
                // click类型
                if($new_arr[$count]['type'] == 'click'){
                    $new_arr[$count]['key'] = $v['value'];
                }elseif($new_arr[$count]['type'] == 'view'){
                    //跳转URL类型
                    $new_arr[$count]['url'] = $v['value'];
                }else{
                    //其他事件类型
                    //$new_arr[$count]['key'] = $key_map[$v['type']];
                    $new_arr[$count]['key'] = $v['value'];  //2016年9月29日01:40:13
                }
            }
            $count++;
        }
        return json_encode(array('button'=>$new_arr),JSON_UNESCAPED_UNICODE);
    }

    /****************************文本回复********************************/
    public function text(){
        if(request()->isPost()) {
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list = Db::table(config('database.prefix').'wx_keyword')->alias('k')
                ->join(config('database.prefix').'wx_text t', 't.id = k.pid', 'left')
                ->field('k.id,k.keyword,t.text')
                ->where('type', 'TEXT')
                ->order('t.createtime DESC')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return ['code'=>0,'data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }
    // 添加文本回复
    public function addText(){
        if(request()->isPost()) {
            $data =  input('post.');
            //添加模式
            $data['createtime'] = time();
            $data['pid'] = db('wx_text')->insertGetId($data);
            unset($data['text']);
            unset($data['createtime']);
            $data['type'] = 'TEXT';
            db('wx_keyword')->insert($data);
            $result['code'] = 1;
            $result['msg'] = '添加成功!';
            $result['url'] = url('text');
            return $result;
        }else{
            $this->assign('title', lang('add') . '文本回复');
            $this->assign('info', 'null');
            return $this->fetch('textForm');
        }
    }

    public function delText(){
        $id = input('id');
        $row = db('wx_keyword')->where(array('id'=>$id))->find();
        if($row){
            db('wx_keyword')->where(array('id'=>$id))->delete();
            db('wx_text')->where(array('id'=>$row['pid']))->delete();
        }
        return ['code'=>1,'msg'=>'删除成功！'];
    }
    //修改文本回复
    public function editText(){
        if(request()->isPost()) {
            $map['id'] = input('post.id');
            $data['keyword'] =  input('post.keyword');
            db('wx_keyword')->where($map)->update($data);

            $where['id'] = db('wx_keyword')->where($map)->value('pid');
            $data['text'] = input('post.text');//添加模式
            $data['updatetime'] = time();
            $data['pid'] = db('wx_text')->where($where)->update($data);

            $result['code'] = 1;
            $result['msg'] = '修改成功!';
            $result['url'] = url('text');
            return $result;
        }else{
            $info = Db::table(config('database.prefix').'wx_keyword')->alias('k')
                ->join(config('database.prefix').'wx_text t','t.id = k.pid','left')
                ->field('k.id,k.keyword,t.text')
                ->where('k.id',input('id'))
                ->find();
            $this->assign('title', lang('edit') . '文本回复');
            $this->assign('info',json_encode($info,true));
            return $this->fetch('textForm');
        }
    }

    /****************************图文回复********************************/
    //图文列表
    public function img(){
        if(request()->isPost()) {
            $page =input('page')?input('page'):1;
            $pageSize =input('limit')?input('limit'):config('pageSize');
            $list=Db::table(config('database.prefix').'wx_keyword')->alias('k')
                ->join(config('database.prefix').'wx_img i','i.id = k.pid','left')
                ->field('k.id,k.keyword,i.title,i.url,i.pic,i.desc')
                ->where('type','IMG')
                ->order('i.createtime DESC')
                ->paginate(array('list_rows'=>$pageSize,'page'=>$page))
                ->toArray();
            return ['code'=>0,'data'=>$list['data'],'count'=>$list['total'],'rel'=>1];
        }
        return $this->fetch();
    }

    // 添加图文列表
    public function addImg(){
        if(request()->isPost()) {
            $add['keyword'] =  db('post.keyword');
            $data = input('post.');
            //添加模式
            $data['createtime'] = time();
            $id =  db('wx_img')->insertGetId($data);
            $data['pid'] = $id;
            $data['type'] = 'IMG';
            if(db('wx_keyword')->insert($data)){
                $result['code'] = 1;
                $result['msg'] = '添加成功!';
                $result['url'] = url('img');
            }else{
                $result['code'] = 0;
                $result['msg'] = '添加失败!';
            }
            return $result;
        }else{
            $this->assign('title', lang('add') . '图文回复');
            $this->assign('info', 'null');
            return $this->fetch('imgForm');
        }

    }

    //删除图文列表
    public function delImg(){
        $id = input('id');
        $row = db('wx_keyword')->where(array('id'=>$id))->find();
        if($row){
            db('wx_keyword')->where(array('id'=>$id))->delete();
            db('wx_img')->where(array('id'=>$row['pid']))->delete();
        }
        return ['code'=>1,'msg'=>'删除成功！'];
    }

    public function editImg(){
        if(request()->isPost()) {
            $map['id'] = input('post.id');
            $data['keyword'] =  input('post.keyword');
            db('wx_keyword')->where($map)->update($data);

            $where['id'] = db('wx_keyword')->where($map)->value('pid');

            $data['title'] = input('post.title');
            $data['url'] = input('post.url');
            $data['pic'] = input('post.pic');
            $data['desc'] = input('post.desc');
            $data['updatetime'] = time();
            $data['pid'] = db('wx_img')->where($where)->update($data);
            $result['code'] = 1;
            $result['msg'] = '修改成功!';
            $result['url'] = url('img');
            return $result;
        }else{
            $info = Db::table(config('database.prefix').'wx_keyword')->alias('k')
                ->join(config('database.prefix').'wx_img i','i.id = k.pid','left')
                ->field('k.id,k.keyword,i.title,i.url,i.pic,i.desc')
                ->where('k.id',input('id'))
                ->find();
            $info['thumb'] = imgUrl($info['pic']);

            $this->assign('title', lang('edit') . '图文回复');
            $this->assign('info',json_encode($info,true));
            return $this->fetch('imgForm');
        }
    }
}