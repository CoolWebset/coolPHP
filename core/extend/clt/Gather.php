<?php
namespace clt;

use clt\Gather;
use think\Db;
use think\Request;

class Gather
{
    private $url;
    private $path;
    private function ksort($arr)
    {
        foreach ($arr as $value) {
            $temp [] = $value;
        }
        return $temp;
    }
    public function __construct($url, $path)
    {
        $this->path = $path;
        $this->url = $url;
        // 设置脚本执行不超时
        set_time_limit ( 0 );
    }
    public function fetch()
    {
        //echo $this->url;
        
        $list = db('wdjcontent')->field('wid')->where('url ="'.$this->url.'"')->find();
        if ($list) {
            return $list['wid'];
        } else {
            $UrlRoot = $this->getUrlRoot($this->url);
            switch ($UrlRoot) {
                case 'mp.weixin.qq.com':
                    return $this->wxtransform ( $this->url, $this->path );
                    break;
                case 'kuaibao.qq.com':
                    return $this->kbtransform ( $this->url, $this->path );
                    break;
                case 'wxn.qq.com':
                    return $this->qqtransform ( $this->url, $this->path );
                    break;
                case 'view.inews.qq.com':
                    return $this->qqtransform ( $this->url, $this->path );
                    break;
                default:
                    return $this->wxtransform ( $this->url, $this->path );
                    break;
            }
        }
    }
    private function getUrlRoot($url)
    {
        #添加头部和尾巴
        $url = $url . "/";
        $arr=parse_url($url);
        return $arr['host'];
    }

    private function createPic($url, $path, $name)
    {
        //echo $name;
        $img = @file_get_contents($url);
        $info = @getimagesize ( $url );
        $type = str_replace ( 'image/', '', $info ['mime'] );
        //echo $type.'--'.$info ['mime'].'--'.$fileName.'<br/>';
        if ($type) {
            $fileName = $path . '/' . $name .".". $type;
            file_put_contents ( $fileName, $img );
        } else {
            $typearr = explode('=', $url);
            $type = $typearr[1];
            echo $type ;
            exit;
            $fileName = $path . '/' . $name .".". $type;
            file_put_contents ( $fileName, $img );
        }
        
        return $fileName;
    }
    //天天快报抓取
    private function kbtransform($url, $path)
    {
        if (! file_exists ( $path )) {
            mkdir ( $path );
        }
        $data ['url'] = $url; // 文章URL
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $content = file_get_contents($url, false, stream_context_create($arrContextOptions));
        //echo $content;
        preg_match ( '/\'title\'\: \'(.*)\'\, /i', $content, $result );
        $data ['title'] = $result [1]; // 文章标题

        preg_match ( '/\'desc\'\: \'(.*)\'\, /i', $content, $result );
        $data ['msg_desc'] = $result [1]; // 文章简介

        preg_match ( '/var newsId \= \"(.*)\"\; /i', $content, $result );
        $newsId = $result [1]; // 文章ID

        preg_match ( '/\'img_url\'\: \'(.*)\'\, /i', $content, $result );
        $data ['cover'] = $this->createPic ( $result [1], $path, "round_head_img_" . $newsId ); // 文章缩略图
        $data ['round_head_img'] = $this->createPic ( $result [1], $path, "round_head_img_" . $newsId );

        preg_match ( '/\'src\'\: \'(.*)\'/i', $content, $result );
        $data ['nickname'] = $result [1]; // 文章作者

        preg_match ( '/<div class=\"content\-box\".*?>.*?<\/div>/ism', $content, $result );
        //dump($result);
        //src="http://inews.gtimg.com/newsapp_match/0/2132892792/0"
        preg_match_all ( '/src=\"[a-zA-z]+:\/\/inews.gtimg.com\/newsapp_match\/\d\/\d*\/\d\"/is', $result [0], $result2 );
        if (! empty ( $result2 [0] )) {
            foreach ($result2 [0] as $value) {
                // 取出防盗链地址中的data-src值后的http://url主体部分
                preg_match ( '/[a-zA-z]+:\/\/inews.gtimg.com\/newsapp_match\/\d\/\d*\/\d/is', $value, $temp );
                dump($temp);
                $temp = explode("/", $temp[0]);
                $temp = $temp[5];
                $nameList [] = $temp;
            }
            foreach ($result2 [0] as $value) {
                preg_match ( '/[a-zA-z]+:\/\/inews.gtimg.com\/newsapp_match\/\d\/\d*\/\d/is', $value, $temp );
                $temp = array_filter ( $temp );
                $temp = $this->ksort ( $temp );
                //echo $temp [0].'<br/>';
                $urlList [] = $temp [0];
            }
            //dump($nameList);
            foreach ($urlList as $value) {
                $name = array_shift ( $nameList );

                $fileName = $this->createPic ($value, $path, $name); // 保存为本地图片
                $urlList2 [] = $fileName;
                //echo $fileName.'/'.$value.'<br>';
                $result [0] = str_replace ( $value, $fileName, $result [0] );
            }
        }
        
        $data ['content'] =  trim($result [0]); // 内容
        
        $data ['addtime'] = time();
        $data ['hits'] = 0;
        //dump($data);exit;
        $res = db('wdjcontent')->insertGetId($data);
        if ($res) {
            return $res;
        } else {
            echo "error";
        }
    }
    //微信新闻抓取
    private function wxtransform($url, $path)
    {
        if (! file_exists ( $path )) {
            mkdir ( $path );
        }
        $data ['url'] = $url; // 文章URL
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
            'http'=>array(
            'method'=>"GET",
            'timeout'=>60,
            )
        );
        $content = file_get_contents($url, false, stream_context_create($arrContextOptions));
        echo $content;
        preg_match ( '/<title>(.*)<\/title>/i', $content, $result );
        $data ['title'] = $result [1]; // 文章标题
        preg_match ( '/var\s+nickname\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['nickname'] = $result [1]; // 公众号昵称
        preg_match ( '/var\s+ct\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['ct'] = $result [1]; // 公众号发布的时间戳
        preg_match ( '/var\s+user_name\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['user_name'] = $result [1]; // 公众号的原始ID
        preg_match ( '/var\s+msg_cdn_url\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['cover'] = $this->createPic ( $result [1], $path, "cover_img_" . $data ['user_name'] );// 封面
        preg_match ( '/var\s+round_head_img\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['round_head_img'] = $this->createPic ( $result [1], $path, "round_head_img_" . $data ['user_name'] ); // // 公众号头像
        preg_match ( "/s?__biz=(.*)&mid=/i", $url, $result );
        $data ['bizId'] = $result [1]; // 公众号BizId
        preg_match ( '/var\s+msg_desc\s*=\s*"([^\s]*)"/', $content, $result );
        $data ['msg_desc'] = $result [1]; // 公众号文章摘要
        // 获取微信主体内容
        preg_match ( '/<div\s+class="rich_media_content\s*"\s+id="js_content">(.*?)<\/div>/is', $content, $result ); //注意非贪婪的?
        preg_match_all ( '/data-src="[a-zA-z]+:\/\/[^\s]*[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/[^\s]*\/\d+\?[^\s]*"|background-image\s*:\s*url\s*\(&quot;[a-zA-z]+:\/\/[^\s]*[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/[^\s]*\/\d+\?[^\s]*\);/is', $result [1], $result2 );
        //dump($result2);exit;
        // 判断微信主体中是否包含防盗链图片
        if (! empty ( $result2 [0] )) {
            foreach ($result2 [0] as $value) {
                // 取出防盗链地址中的data-src值后的http://url主体部分
                preg_match ( '/[a-zA-z]+:\/\/[^\s]*\/[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/([^\s\/]*)\/\d+\?[^\s"]*|[a-zA-z]+:\/\/[^\s]*[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/([^\s\/]*)\/\d+/', $value, $temp );
                $temp = array_filter ( $temp );
                $temp = $this->ksort ( $temp );
                $nameList [] = $temp [1];
            }
            foreach ($result2 [0] as $value) {
                preg_match ( '/[a-zA-z]+:\/\/[^\s]*[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/([^\s\/]*)\/\d+\?[a-zA-z]*\=[a-zA-z]*/is', $value, $temp );
                $temp = array_filter ( $temp );
                $temp = $this->ksort ( $temp );
                //echo $temp [0].'<br/>';
                $urlList [] = $temp [0];
            }
            //dump($urlList);
            foreach ($urlList as $value) {
                $name = array_shift ( $nameList );
                $value = str_replace("\"", "", $value);
                $value1 = str_replace ('https', 'http', $value );
                $fileName = $this->createPic ($value1, $path, $name); // 保存为本地图片
                $urlList2 [] = $fileName;
                $result [1] = str_replace ( $value, $fileName, $result [1] );
                $result [1] = str_replace ( 'data-src', 'src', $result [1] );
            }
        }
        // 返回处理后的微信主体内容。
        $data ['content'] = trim($result [1]);
        $data ['content'] = preg_replace ( '/[a-zA-z]+:\/\/[^\s]*\/[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/([^\s\/]*)\/\d+\?[^\s"]*|[a-zA-z]+:\/\/[^\s]*[mmbiz|mmbiz_jpg|mmbiz_gif|mmbiz_png]\/([^\s\/]*)\/\d+/', './image/defult/defult', $data ['content'] );
        $data ['addtime'] = time();
        $data ['hits'] = 0;
        $res = db('wdjcontent')->insertGetId($data);
        if ($res) {
            return $res;
        } else {
            echo "error";
        }
    }

    private function qqtransform($url, $path)
    {
        if (! file_exists ( $path )) {
            mkdir ( $path );
        }
        $data ['url'] = $url; // 文章URL
        $arrContextOptions=array(
            "ssl"=>array(
                "verify_peer"=>false,
                "verify_peer_name"=>false,
            ),
        );
        $content = file_get_contents($url, false, stream_context_create($arrContextOptions));

        preg_match ( '/<h1>(.*?)<\/h1>/isxu', $content, $result );
        $data ['title'] = $result [1]; // 获取整页内容

        preg_match ( '/<div[^>]*class="loading">(.*?)<\/body>/isxu', $content, $result );
        $data ['content'] = $result [1]; // 获取整页内容
        //dump($content);
        //<span class="origin">腾讯视频</span>
        preg_match ( '/<span[^>]*class="origin">(.*?)<\/span>/isxu', $content, $result );
        $data ['nickname'] = $result [1]; // 公众号昵称
        
        preg_match ( '/<span[^>]*class="date">(.*?)<\/span>/isxu', $content, $result );
        $data ['ct'] = $result [1]; // 公众号发布的时间戳
        //id : "WXN2017100702490900",
        preg_match ( '/cid \:\"(.*?)\"/', $content, $result );
        $data ['id'] = $result [1];
        //img: "//inews.gtimg.com/newsapp_ls/0/2135076903_640320/0",
        preg_match ( '/img\:\s\"(.*?)\"/', $content, $result );
        $data ['cover'] = $this->createPic ( 'http:'.$result [1], $path, "cover_img_" . $data ['id'] );// 封面
        $data ['round_head_img'] = $data ['cover'];
        //desc: "千万只红螃蟹迁徙覆盖整个海岛 街道如同铺红毯(视频)",
        preg_match ( '/desc\:\s\"(.*?)\"/', $content, $result );
        $data ['msg_desc'] = $result [1];

        $data ['addtime'] = time();
        $data ['hits'] = 0;
        $res = db('wdjcontent')->insertGetId($data);
        if ($res) {
            return $res;
        } else {
            echo "error";
        }
    }
}
