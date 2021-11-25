<?php
//跨域访问接口放行
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
defined('IN_PHPCMS') or exit('No permission resources.');
// pc_base::load_app_class('foreground');
pc_base::load_sys_class('format', '', 0);
pc_base::load_sys_class('form', '', 0);
pc_base::load_sys_class('param', '', 0);
require_once 'AipFace.php';
// require 'AipFace.php';
const APP_ID = '';
const API_KEY = '';
const SECRET_KEY = '';
const TIME = '172800';
 // 你的 APPID AK SK

 define('_WEB_',rtrim($_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'],'/').'/');
// CSS 
define('_CSS_',_WEB_.'cms/css/');
// JS 
define('_JS_',_WEB_.'cms/js/');
// img
define('_IMG_',_WEB_.'cms/images/');
// 原图片动态路径
define('_UPLOAD_',_WEB_.'uploads/');
// 缩略图图片动态路径
define('_THUMB_',_WEB_.'thumb/');
// 2  放  D://a/b/c
define('_ROOT_',rtrim($_SERVER['DOCUMENT_ROOT'],'/').'/');
define('_UPLOADS_',_ROOT_.'uploads/');//文件上传
define('_THUMBS_',_ROOT_.'thumb/');//缩略图
// echo _UPLOADS_;exit;
// pre($_SERVER['DOCUMENT_ROOT']);



class baidu extends  AipFace{

    /**
 * 片base64解码
 * @param string $img 来源地址
 * @param bool $url    文件保存路径
 * @param string $thumpath  缩略图名称
 * author csw
 * @return bool|string
 */



    public function thumb_img($img,$son_width,$son_height,$url,$thumpath){
        $filename=$img;
        $info=getimagesize($filename);
        // print_r($info);
        $width=$info[0];
        $height=$info[1];
        // 打开图片
        if($info[2]==1){
            $parent=imagecreatefromgif($filename);
        }elseif($info[2]==2){
            $parent=imagecreatefromjpeg($filename);
        }elseif($info[2]==3){
            $parent=imagecreatefrompng($filename);
        }
        // 创建新的图层
        // $son_width=300;
        // $son_height=50;
        // 等比例缩放
        // $son_height=ceil(($height*$son_width)/$width);
        // 新建图像
        $son=imagecreatetruecolor($son_width,$son_height);
        // $son新建图像
        // $parent原图像
        // 0,0 目标图片的y轴和x轴
        // 0,0 原图片的y轴和x轴
        imagecopyresized($son,$parent,0,0,0,0,$son_width,$son_height,$width,$height);
        // 获取后缀名
        $path=pathinfo($filename,PATHINFO_EXTENSION);
        // 设置文件名
        // $pathname=mt_rand(1000,9999).'.'.$path;
        $pathname=$thumpath;
        $dir=date("Y-m/d");
        if(!is_dir($url."/".$dir)){
            mkdir($url."/".$dir,0700,true);
        }
        $news_filename=$dir."/".$pathname;
        // dump($news_filename);exit;
        $pathname=$url."/".$news_filename;
        
        // 生成图片
        if($info[2]==1){
            imagegif($son,$pathname);
        }elseif($info[2]==2){
            imagejpeg($son,$pathname);
        }elseif($info[2]==3){
            imagepng($son,$pathname);
        }
        // 销毁原图片
        imagedestroy($parent);
        // 销毁目标图片
        imagedestroy($son);
        return $news_filename;
    }
    


    /**
 * 片base64解码
 * @param string $base64_image_content 图片文件流
 * @param bool $save_img    是否保存图片
 * @param string $path  文件保存路径
 * @return bool|string
 */
 public   function imgBase64Decode($base64_image_content = '',$save_img = false,$path=''){
    if(empty($base64_image_content)){
        return false;
    }
 
    //匹配出图片的信息
    $match = preg_match('/^(data:\s*image\/(\w+);base64,)/', $base64_image_content, $result);
    if(!$match){
        return false;
    }
 
    //解码图片内容(方法一)
    /*$base64_image = preg_split("/(,|;)/",$base64_image_content);
    $file_content = base64_decode($base64_image[2]);
    $file_type = substr(strrchr($base64_image[0],'/'),1);*/
 
    //解码图片内容(方法二)
    $base64_image = str_replace($result[1], '', $base64_image_content);
    $file_content = base64_decode($base64_image);
    $file_type = $result[2];
 
    //如果不保存文件,直接返回图片内容
    if(!$save_img){
        return $file_content;
    }
 
    //如果没指定目录,则保存在当前目录下
    if(empty($path)){
        $path = __DIR__;
    }
    $file_path = $path."/".date('Ymd',time())."/";
    if(!is_dir($file_path)){
        //检查是否有该文件夹，如果没有就创建
        mkdir($file_path,0777,true);
    }
    $file_name = time().".{$file_type}";
    $new_file = $file_path.$file_name;
    if(file_exists($new_file)){
        //有同名文件删除
        @unlink($new_file);
    }
    if (file_put_contents($new_file, $file_content)){
        return $new_file;
    }
    return false;
}

    public function index()//人脸注册
    {      
        header("Content-Type: text/html; charset=utf8");

if($_POST){


        $client = new AipFace(APP_ID, API_KEY, SECRET_KEY);
        // $image = "http://photocdn.sohu.com/20090414/Img263384356.jpg";
        $image = $_POST['img_face'];
        // $imageType = "URL";
       
        $imageName = "25220_".date("His",time())."_".rand(1111,9999).'.png';
        if (strstr($image,",")){
            $image = explode(',',$image);
            $image = $image[1];
        }

        $path = "tmp/signImage/".date("Ymd",time());
        if (!is_dir($path)){ //判断目录是否存在 不存在就创建
            mkdir($path,0777,true);
        }
        $imageSrc=  $path."/". $imageName;  //图片名字
        

        $r = file_put_contents($imageSrc, base64_decode($image));//返回的是字节数
        if (!$r) {
            $tmparr1=array('data'=>null,"code"=>1,"msg"=>"图片生成失败");
                json_encode($tmparr);
           }else{
            $tmparr2=array('data'=>1,"code"=>0,"msg"=>"图片生成成功");
                json_encode($tmparr2);
           }  


        $image = APP_PATH.$imageSrc;
        $img = APP_PATH.$imageSrc;      //图片来源地址
        $info = getimagesize($img); //取得图像参数
        $son_w = $info[0];
        $son_h = $info[1];
        $son_width = 640;   //缩略图的宽
        $son_height = $son_h*$son_width/$son_w;  //缩略图的高
        $url = _THUMBS_;   //缩略图存放路径
        $thumpath = substr(strrchr($imageSrc, '/'),1); //缩略图的名称
        $image = $this->thumb_img($img,$son_width,$son_height,$url,$thumpath); //我们要的数据 缩略图
        $image = _THUMB_.$image;





        $imageType = "URL";  
        // 调用人脸检测
        // $client->detect($image, $imageType);
        // 如果有可选参数
        $options = array();
        $options["face_field"] = "beauty,glasses,gender,expression,age";
        $options["max_face_num"] = 2;
        $options["face_type"] = "LIVE";
        $options["liveness_control"] = "LOW";
        // 带参数调用人脸检测
        $re = $client->detect($image, $imageType, $options);
        // pre($re);
        if($_POST['fs']=='app'){//app 人脸注册
            $userid = $_POST['userid'];
        }else{
            $userid = param::get_cookie('_userid');    
        }   
        $face_token=$re['result']['face_list']['0']['face_token'];//人脸标识码
        if(isset($face_token)  && !empty($face_token)){
            // echo $face_token;
            // echo $userid;
            $imageType = "URL";
            $groupId = "8371";
            $userId = $userid;
            // // 调用人脸注册
            $add = $client->addUser($image, $imageType, $groupId, $userId);
            echo $add['error_code'];
            // pre($add);
            // 如果有可选参数
            // $options = array();
            // $options["user_info"] = "user's info";
            // $options["quality_control"] = "NORMAL";
            // $options["liveness_control"] = "LOW";
            // $options["action_type"] = "REPLACE";
            // // 带参数调用人脸注册
            // $add = $client->addUser($image, $imageType, $groupId, $userId, $options);
            if($add['error_code']==0){
                $conn = @mysql_connect('localhost','root','root')or die('link error');
                // 设置数据库的编码
                mysql_set_charset('utf8');
                // 选择数据库
                mysql_select_db('v9')or die('select error');
                $sql = "update  v9_member set `facepath` = "." '{$face_token}' "." where userid = ".$userid;
                $bool = mysql_query($sql);	
                //  if($bool && mysql_affected_rows()){
                    
                // }
                mysql_close($conn);
            }
           
            
        }
    }
        // include template('member', 'face2');
    }

    private function _session_start() {
        $session_storage = 'session_'.pc_base::load_config('system','session_storage');
        pc_base::load_sys_class($session_storage);
      }

      	/**
	 * 初始化phpsso
	 * about phpsso, include client and client configure
	 * @return string phpsso_api_url phpsso地址
	 */
	private function _init_phpsso() {
		pc_base::load_app_class('client', '', 0);
		define('APPID', pc_base::load_config('system', 'phpsso_appid'));
		$phpsso_api_url = pc_base::load_config('system', 'phpsso_api_url');
		$phpsso_auth_key = pc_base::load_config('system', 'phpsso_auth_key');
		$this->client = new client($phpsso_api_url, $phpsso_auth_key);
		return $phpsso_api_url;
    }
    

//     /**
//  * 网络图转base64编码
//  * @param img 图片网址
//  **/
// public function imgToBase64($img = '')
// {
//     if (!$img) {
//         return false;
//     }
//     $imageInfo = getimagesize($img);
//     $base64 = "" . chunk_split(base64_encode(file_get_contents($img)));
//     return 'data:' . $imageInfo['mime'] . ';base64,' . chunk_split(base64_encode(file_get_contents($img)));
// }



  
    public function login()//人脸登陆
    {   
        // echo 1;
        // //exit;
        // echo JS_PATH;
        header("Content-Type: text/html; charset=utf8");
        if($_POST){

        

        $client = new AipFace(APP_ID, API_KEY, SECRET_KEY);
        // $image = "http://photocdn.sohu.com/20090414/Img263384356.jpg";
        // $imageType = "URL";

       $image = $_POST['img_face'];

       if($_POST['fs']=='app'){
       //echo  $image = imgToBase64($image);
       //exit;
       }
        $imageName = "25220_".date("His",time())."_".rand(1111,9999).'.png';
        if (strstr($image,",")){
            $image = explode(',',$image);
            $image = $image[1];
        }

        $path = "tmp/signImage/".date("Ymd",time());
        if (!is_dir($path)){ //判断目录是否存在 不存在就创建
            mkdir($path,0777,true);
        }
        $imageSrc=  $path."/". $imageName;  //图片名字
        

        $r = file_put_contents($imageSrc, base64_decode($image));//返回的是字节数
        if (!$r) {
            $tmparr1=array('data'=>null,"code"=>1,"msg"=>"图片生成失败");
                json_encode($tmparr);
           }else{
            $tmparr2=array('data'=>1,"code"=>0,"msg"=>"图片生成成功");
                json_encode($tmparr2);
           }  


           $image = APP_PATH.$imageSrc;
           $img = APP_PATH.$imageSrc;      //图片来源地址
        // if($_POST['fs']=='app'){
        //     // $img = $_POST['img_face']; 
        // }
        $info = getimagesize($img); //取得图像参数
        $son_w = $info[0];
        $son_h = $info[1];
        $son_width = 640;   //缩略图的宽
        $son_height = $son_h*$son_width/$son_w;  //缩略图的高
        $url = _THUMBS_;   //缩略图存放路径
        $thumpath = substr(strrchr($imageSrc, '/'),1); //缩略图的名称
        $image = $this->thumb_img($img,$son_width,$son_height,$url,$thumpath); //我们要的数据 缩略图
        $image = _THUMB_.$image;
           
        
        $imageType = "URL";        
        $groupIdList = "8371";
        // if($_POST['fs']=='app'){
        //     echo 1;
        //     $image = $_POST['img_face'];
        //     echo  $image = base64_encode(file_get_contents($image));
        //     // $imageType = "BASE64"; 
        // }
        // 调用人脸搜索
        // $client->search($image, $imageType, $groupIdList);
        // 如果有可选参数
        //echo $image;
       
        $options = array();
        $options["max_face_num"] = 3;
        $options["match_threshold"] = 70;
        $options["quality_control"] = "NORMAL";
        $options["liveness_control"] = "LOW";
        $options["max_user_num"] = 3;
        // 带参数调用人脸搜索
        $sear =  $client->search($image, $imageType, $groupIdList, $options);
        $face_token = $sear['result']['face_token'];
        $userid = $sear['result']['user_list']['0']['user_id'];

        if($_POST['fs']=='app' && $sear['error_code']==0){//app登陆陆返回值

            $conn = @mysql_connect('localhost','root','root')or die('link error');
            // 设置数据库的编码
            mysql_set_charset('utf8');
            // 选择数据库
            mysql_select_db('v9')or die('select error');
          
            $sql = "select * from v9_member where userid = " .$userid;//. " and facepath = "." '{$face_token}' ";
            $re = getOne($sql);
            $data['success'] = 1;//登陆成功
            $data['datas'] = $re;
            mysql_close($conn);
            echo json_encode($data);exit;

        }else{//网页版登陆返回值

            echo $sear['error_code'];
        }
    //    exit;
    
        if(isset($face_token) && !empty($face_token) && isset($userid) && !empty($userid)){
            $conn = @mysql_connect('localhost','root','yl@83713483,120@#￥%HGd*%')or die('link error');
            // 设置数据库的编码
            mysql_set_charset('utf8');
            // 选择数据库
            mysql_select_db('v9')or die('select error');
          
            $sql = "select * from v9_member where userid = " .$userid;//. " and facepath = "." '{$face_token}' ";
            $r = getOne($sql);
            if($r){
            //    pre($r);
                $this->_session_start();
                //获取用户siteid
               $siteid = isset($_REQUEST['siteid']) && trim($_REQUEST['siteid']) ? intval($_REQUEST['siteid']) : 1;
                //定义站点id常量
                if (!defined('SITEID')) {
                   define('SITEID', $siteid);
                }
               // echo TIME + time();
               // echo 1;
                $userid = $r['userid'];
                $groupid = $r['groupid'];
                $username = $r['username'];
                $timeout =  $r['lastdate'];
                $password =  $r['password'];
                $face = 1;

                
               $nickname = empty($r['nickname']) ? $username : $r['nickname'];
               if(CHARSET != 'utf-8') {//转编码
                    $nickname = iconv('utf-8', CHARSET, $nickname);
                } else {
                    $nickname =$nickname;
                }

    
                // $this->db->update($updatearr, array('userid'=>$userid));
    
                if(!isset($cookietime)) {
                    $get_cookietime = param::get_cookie('cookietime');
                }
                $_cookietime = $cookietime ? intval($cookietime) : ($get_cookietime ? $get_cookietime : 0);
                $cookietime = $_cookietime ? SYS_TIME + $_cookietime +3600 : 0;
                $phpcms_auth_key = md5(pc_base::load_config('system', 'auth_key').$this->http_user_agent);
                $phpcms_auth = sys_auth($userid."\t".$password, 'ENCODE', $phpcms_auth_key);
                pc_base::load_sys_class('param', '', 0);
                pc_base::load_config('system', 'phpsso');
                $this->_init_phpsso();
                param::set_cookie('auth', $phpcms_auth, $cookietime);
                $phpcms_auth = param::get_cookie('auth');
                // $cookietime = $_cookietime;
                param::set_cookie('_userid', $userid, $cookietime);
                param::set_cookie('_username', $username, $cookietime);
                param::set_cookie('_groupid', $groupid, $cookietime);
                param::set_cookie('cookietime', $_cookietime, $cookietime);
                param::set_cookie('_nickname', $nickname, $cookietime);
                param::set_cookie('_password', $password, $cookietime);
                param::set_cookie('_face', $face, $cookietime);
                userpermission($userid);
                // pre($_COOKIE);

                $forward = isset($_GET['forward']) && !empty($_GET['forward']) ? $_GET['forward'] : 'index.php?m=member&c=index';
               
                exit;

                

              
            }else{
                echo '登陆失败';
                exit;
            }

            mysql_close($conn);
        }
    }
            // include template('member', 'face3');

    }






}
















?>