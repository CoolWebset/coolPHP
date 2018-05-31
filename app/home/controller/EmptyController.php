<?php
namespace app\home\controller;
use think\Db;
use think\Request;
use clt\Form;
class EmptyController extends Common{
    protected  $dao,$fields;
    public function _initialize()
    {
        parent::_initialize();
        $this->dao = db(DBNAME);
    }
    public function index(){
        if(DBNAME=='page'){
            $info = $this->dao->where('id',input('catId'))->find();
            $this->assign('info',$info);
            if($info['template']){
                $template = $info['template'];
            }else{
                $info['template'] = db('category')->where('id',$info['id'])->value('template_show');
                if($info['template']){
                    $template = $info['template'];
                }else{
                    $template = DBNAME.'_show';
                }
            }
            return $this->fetch($template);
        }else{
            $map['catid'] = input('catId');
            if(DBNAME=='blog'){
                $listnav = db('category')->where(array('parentid'=>14))->field(['id','catname'])->select();
                foreach($listnav as $k=>$v){
                    $listnav[$k]['num']= db('blog')->where(array('catid'=>$v['id']))->count();
                    $str .=','.$v['id'];
                }
                $this->assign('listnav',$listnav);
                if($map['catid']==14){
                    $map ='catid in('.substr($str,1).')';
                }
                $list=$this->dao->alias('a')
                ->join(config('database.prefix').'category c','a.catid = c.id','left')
                ->where($map)
                ->field('a.*,c.catdir')
                ->order('listorder asc,createtime desc')
                ->paginate($this->pagesize);
                //echo $this->dao->getlastsql();exit;
                // 获取分页显示
                $page = $list->render();
                $list = $list->toArray();
                foreach ($list['data'] as $k=>$v){
                    $list['data'][$k]['controller'] = $v['catdir'];
                    $list_style = explode(';',$v['title_style']);
                    $list['data'][$k]['title_color'] =$list_style[0];
                    $list['data'][$k]['title_weight'] =$list_style[1];
                    $title_thumb = $v['thumb'];
                    $list['data'][$k]['title_thumb'] = $title_thumb?__PUBLIC__.$title_thumb:__HOME__.'/images/portfolio-thumb/p'.($k+1).'.jpg';
                }
                $this->assign('list',$list['data']);
                $this->assign('page',$page);
            }elseif(DBNAME=='picture'){
                $listnav = db('category')->where(array('parentid'=>46))->field(['id','catname','catdir'])->select();
                foreach($listnav as $k=>$v){
                    $listnav[$k]['num']= db('picture')->where(array('catid'=>$v['id']))->count();
                    $str .=','.$v['id'];
                }
                $this->assign('listnav',$listnav);
                // $map ='catid in('.substr($str,1).')';
                $list=$this->dao->alias('a')
                ->join(config('database.prefix').'category c','a.catid = c.id','left')
                ->field('a.*,c.catdir')->where( $map )
                ->order('listorder asc,createtime desc')
                ->paginate($this->pagesize);
                //echo $this->dao->getlastsql();exit;
                // 获取分页显示
                $page = $list->render();
                $list = $list->toArray();
                foreach ($list['data'] as $k=>$v){
                    $list['data'][$k]['controller'] = $v['catdir'];
                    $list_style = explode(';',$v['title_style']);
                    $list['data'][$k]['title_color'] =$list_style[0];
                    $list['data'][$k]['title_weight'] =$list_style[1];
                    $title_thumb = $v['thumb'];
                    $list['data'][$k]['title_thumb'] = $title_thumb?__PUBLIC__.$title_thumb:__HOME__.'/images/portfolio-thumb/p'.($k+1).'.jpg';
                }
                $this->assign('list',$list['data']);
                $this->assign('page',$page);
            }else{

                $list=$this->dao->alias('a')
                    ->join(config('database.prefix').'category c','a.catid = c.id','left')
                    ->where($map)
                    ->field('a.*,c.catdir')
                    ->order('listorder asc,createtime desc')
                    ->paginate($this->pagesize);
                // 获取分页显示
                $page = $list->render();
                $list = $list->toArray();
                foreach ($list['data'] as $k=>$v){
                    $list['data'][$k]['controller'] = $v['catdir'];
                    $list_style = explode(';',$v['title_style']);
                    $list['data'][$k]['title_color'] =$list_style[0];
                    $list['data'][$k]['title_weight'] =$list_style[1];
                    $title_thumb = $v['thumb'];
                    $list['data'][$k]['title_thumb'] = $title_thumb?__PUBLIC__.$title_thumb:__HOME__.'/images/portfolio-thumb/p'.($k+1).'.jpg';
                }
                $this->assign('list',$list['data']);
                $this->assign('page',$page);
            }
			$cattemplate = db('category')->where('id',input('catId'))->value('template_list');
			$template =$cattemplate ? $cattemplate : DBNAME.'_list';
            return $this->fetch($template);
        }
    }
    public function info(){
        $this->dao->where('id',input('id'))->setInc('hits');
        $info = $this->dao->where('id',input('id'))->find();
        $info['pic'] = $info['pic']?__PUBLIC__.$info['pic']:__HOME__."/images/sample-images/blog-post".rand(1,3).".jpg";
        $title_style = explode(';',$info['title_style']);
        $info['title_color'] = $title_style[0];
        $info['title_weight'] = $title_style[1];
        $title_thumb = $info['thumb'];
        $info['title_thumb'] = $title_thumb?__PUBLIC__.$title_thumb:__HOME__.'/images/sample-images/blog-post'.rand(1,3).'.jpg';
        if(DBNAME=='picture'){
            $pics = explode(':::',$info['pics']);
            foreach ($pics as $k=>$v){
                $info['pics'][$k] = explode('|',$v);
            }
        }
        $this->assign('info',$info);
        if($info['template']){
			$template = $info['template'];
		}else{
			$cattemplate = db('category')->where('id',$info['catid'])->value('template_show');
			if($cattemplate){
				$template = $cattemplate;
			}else{
				$template = DBNAME.'_show';
			}
        }
        if(DBNAME=='blog'){
            $listnav = db('category')->where(array('parentid'=>14))->field(['id','catname'])->select();
            foreach($listnav as $k=>$v){
                $listnav[$k]['num']= db('blog')->where(array('catid'=>$v['id']))->count();
                $str .=','.$v['id'];
            }
            $this->assign('listnav',$listnav);
        }elseif(DBNAME == 'case'){
            //echo  $info['bannerzutu'];
        }
        return $this->fetch($template);
    }
    public function load(){
      $thumbgroup = db('picture')->field('thumbgroup')->where('id',input('id'))->find();
      // $info = $this->dao->where('id',input('id'))->find();
      $thumbgroup = substr($thumbgroup['thumbgroup'], 0, -1);
      $thumbgrouparry = explode(';',$thumbgroup);
      foreach ($thumbgrouparry as $key => $value) {
        $thumbgrouparry[$key] = '/public'.$value;
      }
      // dump($thumbgrouparry);
      return json(['data'=>$thumbgrouparry,'code'=>1,'message'=>'操作完成']);
        // $listnav = db('category')->where(array('parentid'=>10))->field(['id','catname'])->select();
        // foreach($listnav as $k=>$v){
        //     $listnav[$k]['num']= db('blog')->where(array('catid'=>$v['id']))->count();
        //     $str .=','.$v['id'];
        // }
        // $map ='catid in('.substr($str,1).')';
        // $list=db('case')->where($map)->paginate(8);
        // //echo db('case')->getlastsql();exit;
        // $page = $list->render();
        // $list = $list->toArray();
        // $this->assign('page',$page);
        // $this->assign('list',$list['data']);
        // return $this->fetch('case_load');
    }

    public function page(){
        $listnav = db('category')->where(array('parentid'=>10))->field(['id','catname'])->select();
        foreach($listnav as $k=>$v){
            $listnav[$k]['num']= db('blog')->where(array('catid'=>$v['id']))->count();
            $str .=','.$v['id'];
        }
        $map ='catid in('.substr($str,1).')';
        $list=db('case')->where($map)->paginate(8);
        //echo db('case')->getlastsql();exit;
        $page = $list->render();
        $list = $list->toArray();
        $this->assign('page',$page);
        $this->assign('list',$list['data']);
        return $this->fetch('case_load');
    }
    
}
