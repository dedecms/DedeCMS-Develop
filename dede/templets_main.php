<?php
/**
 * 模板文件管理
 *
 * @version   $Id: templets_main.php 1 23:07 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . '/config.php';
CheckPurview('plus_文件管理器');

if (empty($acdir)) {
    $acdir = $cfg_df_style;
}

$templetdir = $cfg_basedir . $cfg_templets_dir;
$templetdird = $templetdir . '/' . $acdir;
$templeturld = $cfg_templeturl . '/' . $acdir;

if (preg_match("#\.#", $acdir)) {
    ShowMsg('Not Allow dir ' . $acdir . '!', '-1');
    exit();
}

//获取默认文件说明信息
function GetInfoArray($filename)
{
    $arrs = array();
    $dlist = file($filename);
    foreach ($dlist as $d) {
        $d = trim($d);
        if ($d != '') {
            list($dname, $info) = explode(',', $d);
            $arrs[$dname] = $info;
        }
    }
    return $arrs;
}

$dirlists = GetInfoArray($templetdir . '/templet-dirlist.inc');
$filelists = GetInfoArray($templetdir . '/templet-filelist.inc');
$pluslists = GetInfoArray($templetdir . '/templet-pluslist.inc');
$fileinfos = ($acdir == 'plus' ? $pluslists : $filelists);

$filearray = array();
// 判断$dir定义的路径是否是一个目录
if (is_dir($templetdird)) {
    // 如果是一个目录则打开目录句柄
    if ($dh = opendir($templetdird)) {
        // 循环遍历目录句柄中的所有文件和目录
        while (($file = readdir($dh)) !== false){
            if(preg_match("#\.htm#", $file)) {

                $filetime = filemtime($templetdird.'/'.$file);
                $_file['filename'] = $file;
                $_file['filetime'] = MyDate("Y-m-d H:i", $filetime);
                $_file['fileinfo'] = $fileinfos[$file] ?? '未知模板';
                // echo "目录名：$file 、文件类型：" . filetype($dir.$file) . "<br />";
                $filearray[] = $_file;
            }
        }
        // 关闭目录句柄，这里可以不传入参数，因为最后一次打开的就是$dh。
        closedir($dh);
    }
}
$dlist = new DataListCP();
$dlist->SetParameter("files", $filearray);
$dlist->SetTemplet(DEDEADMIN . "/templets/templets_default.htm");
$dlist->display();
