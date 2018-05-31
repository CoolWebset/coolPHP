<?php
namespace app\admin\controller;
class Yuding extends Common
{
    function _initialize()
    {
        parent::_initialize();
    }
    public function index()
    {

        if(request()->isPost()){
          $type = Input('type');
          $page =input('page')?input('page'):1;
          $pageSize =input('limit')?input('limit'):config('pageSize');
          $list = db('message')->where('type',$type)->paginate(array('list_rows'=>$pageSize,'page'=>$page))
          ->toArray();

          $rsult['code'] = 0;
          $rsult['msg'] = "获取成功";
          $lists = $list['data'];
          foreach ($lists as $k=>$v ){
              $lists[$k]['intime'] = date('Y-m-d H:i:s',$v['intime']);
              $lists[$k]['outtime'] = date('Y-m-d H:i:s',$v['outtime']);
              $lists[$k]['partytime'] = date('Y-m-d H:i:s',$v['partytime']);
          }
          $rsult['data'] = $lists;
          $rsult['count'] = $list['total'];
          $rsult['rel'] = 1;
          return $rsult;
        }else{
          $type = Input('type');
          if($type == 1){
            $title = '客房预订';
          }else{
            $title = '会议预订';
          }
          $this->assign('type', $type);
          $this->assign('title', $title);
        }


        return $this->fetch();
    }

}
