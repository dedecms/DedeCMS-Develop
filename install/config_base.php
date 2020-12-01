<?php 
/*************************************************
本文件的信息不建议用户自行更改，否则发生意外自行负责
**************************************************/
error_reporting(E_ALL || ~E_NOTICE);

define('DEDEINC',dirname(__FILE__));

$ckvs = Array('_GET','_POST','_COOKIE','_FILES');
$ckvs4 = Array('HTTP_GET_VARS','HTTP_POST_VARS','HTTP_COOKIE_VARS','HTTP_POST_FILES');

//PHP小于4.1版本的兼容性处理
$phpold = 0;
foreach($ckvs4 as $_k=>$_v){ 
	if(!@is_array(${$_v})) continue;
	if(!@is_array(${$ckvs[$_k]})){ 
		${$ckvs[$_k]} = ${$_v}; unset(${$_v}); $phpold=1;
	}
}
//全局安全检测
foreach($ckvs as $ckv){
   foreach($$ckv AS $_k => $_v){ 
      if(eregi("^(_|globals|cfg_)",$_k)) unset(${$ckv}[$_k]);
   }
}

//检测上传的文件中是否有危险代码，有直接退出处理
if( !isset($cfg_NoUploadSafeCheck) ){
if (is_array($_FILES)) {
 foreach($_FILES AS $_name => $_value){
    ${$_name} = $_value['tmp_name'];
    $_fp = @fopen(${$_name},'r');
    $_fstr = @fread($_fp,filesize(${$_name}));
    @fclose($_fp);
    if($_fstr!='' && ereg("<(\?|%)",$_fstr)){
       echo "你上传的文件中含有危险内容，程序终止处理！";
       exit();
    }
 }
}}

//载入用户配置的系统变量
require_once(DEDEINC."/config_hand.php");

//设置站点维护状态开启后，程序文件最上方有 $cfg_IsCanView=true; 则该程序仍访问
if(!isset($cfg_IsCanView)) $cfg_IsCanView = false;
if(isset($cfg_websafe_open) && $cfg_websafe_open=='Y' && !$cfg_IsCanView){
	include(DEDEINC.'/alert.htm');
	exit();
}

//php5.1版本以上时区设置
if(PHP_VERSION > '5.1') {
	$time51 = 'Etc/GMT'.($cfg_cli_time > 0 ? '-' : '+').abs($cfg_cli_time);
	function_exists('date_default_timezone_set') ? @date_default_timezone_set($time51) : '';
}


if(!isset($cfg_needFilter)) $cfg_needFilter = false;
$cfg_isUrlOpen = @ini_get("allow_url_fopen");

//安全模式检测
$cfg_isSafeMode = @ini_get("safe_mode");
if($cfg_isSafeMode){
	$cfg_os = (isset($_ENV['OS']) ? strtolower($_ENV['OS']) : '');
	if($cfg_os=='windows_nt') $cfg_isSafeMode = false;
}

if($phpold==1){ //低版本强制检查变量
	$cfg_isMagic = false;
	$cfg_registerGlobals = false;
}else{
	$cfg_registerGlobals = @ini_get("register_globals");
  $cfg_isMagic = @ini_get("magic_quotes_gpc");
}

//检测系统是否注册外部变量
if(!$cfg_isMagic) require_once(DEDEINC."/config_rglobals_magic.php");
else if(!$cfg_registerGlobals || $cfg_needFilter) require_once(DEDEINC."/config_rglobals.php");

//Session保存路径
$sessSavePath = DEDEINC."/../data/sessions/";
if(is_writeable($sessSavePath) && is_readable($sessSavePath)){ session_save_path($sessSavePath); }

//对于仅需要简单ＳＱＬ操作的页面，引入本文件前把此$__ONLYDB设为true，可避免引入不必要的文件
if(!isset($__ONLYDB)) $__ONLYDB = false;
if(!isset($__ONLYCONFIG)) $__ONLYCONFIG = false;

//站点根目录
//$cfg_basedir = str_replace("\\","/",substr(DEDEINC,0,-8));
$ndir = str_replace("\\","/",dirname(__FILE__));
$cfg_basedir = eregi_replace($cfg_cmspath."/include[/]{0,1}$","",$ndir);
if($cfg_multi_site == 'Y') $cfg_mainsite = $cfg_basehost;
else  $cfg_mainsite = "";

//数据库连接信息
$cfg_dbhost = '~dbhost~';
$cfg_dbname = '~dbname~';
$cfg_dbuser = '~dbuser~';
$cfg_dbpwd = '~dbpwd~';
$cfg_dbprefix = '~dbprefix~';
$cfg_db_language = '~db_language~';

//模板的存放目录
$cfg_templets_dir = $cfg_cmspath.'/templets';
$cfg_templeturl = $cfg_mainsite.$cfg_templets_dir;

//插件目录，这个目录是用于存放计数器、投票、评论等程序的必要动态程序
$cfg_plus_dir = $cfg_cmspath.'/plus';
$cfg_phpurl = $cfg_mainsite.$cfg_plus_dir;

//会员目录
$cfg_member_dir = $cfg_cmspath.'/member';
$cfg_memberurl = $cfg_mainsite.$cfg_member_dir;

//会员个人空间目录#new
$cfg_space_dir = $cfg_cmspath.'/space';
$cfg_spaceurl = $cfg_basehost.$cfg_space_dir;

$cfg_medias_dir = $cfg_cmspath.$cfg_medias_dir;
//上传的普通图片的路径,建议按默认
$cfg_image_dir = $cfg_medias_dir.'/allimg';
//上传的缩略图
$ddcfg_image_dir = $cfg_medias_dir.'/litimg';
//专题列表的存放路径
$cfg_special = $cfg_cmspath.'/special';
//用户投稿图片存放目录
$cfg_user_dir = $cfg_medias_dir.'/userup';
//上传的软件目录
$cfg_soft_dir = $cfg_medias_dir.'/soft';
//上传的多媒体文件目录
$cfg_other_medias = $cfg_medias_dir.'/media';
//圈子目录
$cfg_group_path = $cfg_cmspath.'/group';
//书库目录
$cfg_book_path = $cfg_cmspath.'/book';
//问答模块目录
$cfg_ask_path = $cfg_cmspath.'/ask';
//圈子网址
$cfg_group_url = $cfg_mainsite.$cfg_group_path;
//书库网址
$cfg_book_url = $cfg_mainsite.$cfg_book_path;
//问答模块网址
$cfg_ask_url = $cfg_mainsite.$cfg_ask_path;

//软件摘要信息，****请不要删除本项**** 否则系统无法正确接收系统漏洞或升级信息
//-----------------------------
$cfg_softname = "织梦内容管理系统";
$cfg_soft_enname = "DedeCms V5.1 UTF8";
$cfg_soft_devteam = "织梦团队";
$cfg_version = 'v_5_1_UTF8';
$cfg_ver_lang = 'utf-8'; //严禁手工修改此项

//默认扩展名，仅在命名规则不含扩展名的时候调用
$art_shortname = '.html';
//文档的默认命名规则
$cfg_df_namerule = '{typedir}/{Y}{M}/{D}-{aid}.html';
$cfg_df_listnamerule = '{typedir}/{tid}-{page}.html';
//新建目录的权限，如果你使用别的属性，本程不保证程序能顺利在Linux或Unix系统运行
$cfg_dir_purview = 0755;

//引入数据库类和常用函数
require_once(DEDEINC.'/config_passport.php');

if(!$__ONLYCONFIG) include_once(DEDEINC.'/pub_db_mysql.php');
if(!$__ONLYDB) include_once(DEDEINC.'/inc_functions.php');

?>