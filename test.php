<?php
/**
 * Created by PhpStorm.
 * User: rex
 * Date: 2017/7/6 0006
 * Time: 上午 11:32
 * demoUrl  localhost/demo/phpquery/test.php?url=l&list=.TabCss dd dl a&titleTag=#title&conTag=content&br=<br><br>
 */
include 'phpquery/phpQuery/phpQuery.php';
class Collection{
    private $url; //列表页链接
    private $sonUrl;//详情页链接
    private $list;//列表页的a链接所在位置
    private $titleTag;//详情页的标题标签
    private $conTag;//详情页内容所在标签
    private $br;//详情页的换行符
    private $delete;

    public function init(){
        $_GET['url'] ="http://www.bixia.org/59_59857/";
        $_GET['sonUrl'] = "http://www.bixia.org/59_59857";
        $_GET['list'] = ".box_con dl dd a";
        $_GET['titleTag'] = "h1";
        $_GET['conTag'] = "#content";
        $_GET['br'] = "<br>" ;
        $this->setVar();//处理参数
        header("Content-type: text/html; charset=utf-8");
        ini_set('date.timezone','Asia/Shanghai');
        set_time_limit(0);
        $t1 = microtime(true);
        $this->coll();//开始采集
        $t2 = microtime(true);
        $time =  '耗时'.round($t2-$t1,3).'秒<br>';
        error_log(print_r($time,true));
    }
    private function coll(){

        $mxUrl = $this->sonUrl;

        //采集小说章节链接
          phpQuery::newDocumentFile($this->url);    //抓取网址
        //取出页面所有链接排序
        $arr=pq($this->list);                                             //pq类似于jquery的选择器$()，这里找到class为postTitle的元素

        $list = array();
        foreach($arr as $li){
            $list[] =  pq($li)->attr('href');
        }

       // sort($list);//对a标签排序
        $result = '';
        foreach($list as $k=>$v){
            if($k==6){
                break;
            }
            $html = file_get_contents($mxUrl.$v);

            if( strpos($html,'charset="')){
                $html = iconv("gb2312", "utf-8//IGNORE",$html);
            }

             phpQuery::newDocument($html);

            //获取title
            $title =  pq($this->titleTag)->text();

            $con = pq($this->conTag)->html();

            $con =  str_replace($this->br,"\r\n",$con);


            $result .=  $title."\r\n";
            echo $title.'组装完成<br>';
            $result .=  $con."\r\n";
        }


        $filename=time().'.txt';//要导出的文件的文件名需要加上文件后缀
        header('Content-Type: text/x-sql');

        header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');

        header('Content-Disposition: attachment; filename="' .$filename. '"');
        $is_ie = 'IE';
        if ($is_ie == 'IE') {
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
        } else {
            header('Pragma: no-cache');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        }
        echo $result;
        exit();
    }
    private function setVar(){
       $this->url = $this->isValidUrl($_GET['url'])==false ? '' : $_GET['url'];

        if($this->url=='') echo "<script> alert('url错误') </script>";
        $this->sonUrl = $_GET['sonUrl'];
        $this->list = $_GET['list'];
        $this->titleTag = $_GET['titleTag'];
        $this->conTag = $_GET['conTag'];
        $this->br = $_GET['br'];
    }

    public function isValidUrl($url) {
        $patern = '/^http[s]?:\/\/'.
            '(([0-9]{1,3}\.){3}[0-9]{1,3}'.             // IP形式的URL- 199.194.52.184
            '|'.                                        // 允许IP和DOMAIN（域名）
            '([0-9a-z_!~*\'()-]+\.)*'.                  // 三级域验证- www.
            '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\.'.     // 二级域验证
            '[a-z]{2,6})'.                              // 顶级域验证.com or .museum
            '(:[0-9]{1,4})?'.                           // 端口- :80
            '((\/\?)|'.                                 // 如果含有文件对文件部分进行校验
            '(\/[0-9a-zA-Z_!~\*\'\(\)\.;\?:@&=\+\$,%#-\/]*)?)$/';

        if(!preg_match($patern, $url)) {
            return false;
        }else{
            return true;
        }
    }
}
$coll = new Collection();
$coll->init();


