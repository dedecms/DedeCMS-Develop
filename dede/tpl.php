<?php
/**
 * 文件管理器
 *
 * @version   $Id: tpl.php 1 23:44 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('plus_文件管理器');

$action = isset($action) ? trim($action) : '';

if (empty($acdir)) {
    $acdir = $cfg_df_style;
}

$templetdir = $cfg_basedir . $cfg_templets_dir;
$templetdird = $templetdir . '/' . $acdir;
$templeturld = $cfg_templeturl . '/' . $acdir;
if (empty($filename)) {
    $filename = '';
}

$filename = preg_replace("#[\/\\\\]#", '', $filename);
if (preg_match("#\.#", $acdir)) {
    ShowMsg('Not Allow dir ' . $acdir . '!', '-1');
    exit();
}
/*
function edit_new_tpl() { }
编辑模板
 */
if ($action == 'edit' || $action == 'newfile') {
    if ($filename == '' && $action == 'edit') {
        ShowMsg('未指定要编辑的文件', '-1');
        exit();
    }
    if (!file_exists($templetdird . '/' . $filename) && $action == 'edit') {
        $action = 'newfile';
    }

    //读取文件内容
    //$content = dede_htmlspecialchars(trim(file_get_contents($truePath.$filename)));
    if ($action == 'edit') {
        $fp = fopen($templetdird . '/' . $filename, 'r');
        $content = fread($fp, filesize($templetdird . '/' . $filename));
        fclose($fp);
        $content = preg_replace("#<textarea#i", "##textarea", $content);
        $content = preg_replace("#</textarea#i", "##/textarea", $content);
        $content = preg_replace("#<form#i", "##form", $content);
        $content = preg_replace("#</form#i", "##/form", $content);
    } else {
        if (empty($filename)) {
            $filename = 'newtpl.htm';
        }

        $content = '';
    }

    //获取标签帮助信息
    $helps = $dtags = array();
    $tagHelpDir = DEDEINC . '/taglib/help/';
    $dir = dir($tagHelpDir);
    while (false !== ($entry = $dir->read())) {
        if ($entry != '.' && $entry != '..' && !is_dir($tagHelpDir . $entry)) {
            $dtags[] = str_replace('.txt', '', $entry);
        }
    }
    $dir->close();
    foreach ($dtags as $tag) {
        //$helpContent = file_get_contents($tagHelpDir.$tag.'.txt');
        $fp = fopen($tagHelpDir . $tag . '.txt', 'r');
        $helpContent = fread($fp, filesize($tagHelpDir . $tag . '.txt'));
        fclose($fp);
        $helps[$tag] = explode('>>dede>>', $helpContent);
    }

    make_hash();
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN . "/templets/tpl_edit.htm");
    $dlist->display();
    exit();
}
/*---------------------------
function save_tpl() { }
保存编辑模板
--------------------------*/
else if ($action == 'saveedit') {
    csrf_check();
    if ($filename == '') {
        ShowMsg('未指定要编辑的文件或文件名不合法', '-1');
        exit();
    }
    if (!preg_match("#\.htm$#", $filename)) {
        ShowMsg('DEDE模板文件，文件名必须用.htm结尾！', '-1');
        exit();
    }
    $content = stripslashes($content);
    $content = preg_replace("/##textarea/i", "<textarea", $content);
    $content = preg_replace("/##\/textarea/i", "</textarea", $content);
    $content = preg_replace("/##form/i", "<form", $content);
    $content = preg_replace("/##\/form/i", "</form", $content);
    $truefile = $templetdird . '/' . $filename;
    $fp = fopen($truefile, 'w');
    fwrite($fp, $content);
    fclose($fp);
    ShowMsg('成功修改或新建文件', 'templets_main.php?acdir=' . $acdir);
    exit();
}
/*---------------------------
function del_tpl() { }
删除模板
--------------------------*/
else if ($action == 'del') {
    $truefile = $templetdird . '/' . $filename;
    if (unlink($truefile)) {
        ShowMsg('删除文件成功', 'templets_main.php?acdir=' . $acdir);
        exit();
    } else {
        ShowMsg('删除文件失败', '-1');
        exit();
    }
}
/*----------------------
function _upload() {}
上传新模板
-----------------------*/
else if ($action == 'upload') {
    include_once dirname(__FILE__) . '/../include/oxwindow.class.php';
    $acdir = str_replace('.', '', $acdir);
    $win = new OxWindow();
    make_hash();
    $win->Init("tpl.php", "js/blank.js", "POST' enctype='multipart/form-data' ");
    $win->mainTitle = "模块管理";
    $wecome_info = "<ul class='uk-breadcrumb'><li><a href='templets_main.php'>模板管理</a></li><li><span>上传模板</span></li></ul>";
    $win->AddTitle('请选择要上传的文件:');
    $win->AddHidden("action", 'uploadok');
    $msg = "
<table width='600' border='0' cellspacing='0' cellpadding='0'>
<tr>
<td width='96' height='60'>请选择文件：</td>
<td width='504'>
<input name='acdir' type='hidden' value='$acdir'  />
<input name='token' type='hidden' value='{$_SESSION['token']}'  />

<div class='uk-inline uk-form-custom' uk-form-custom='target: true'>
<span class='uk-form-icon uk-icon' uk-icon='icon: upload'></span>
<input name='upfile' type='file' id='upfile' />
<input class='uk-input uk-form-small uk-form-width-large' type='text' placeholder='点击选择文件'>
</div>

</td>
</tr>
</table>
    ";
    $win->AddMsgItem("<div style='padding-left:20px;line-height:150%'>$msg</div>");
    $winform = $win->GetWindow('ok', '');
    $win->Display();
    exit();
}
/*----------------------
function _upload() {}
上传新模板
-----------------------*/
else if ($action == 'uploadok') {
    csrf_check();
    if (!is_uploaded_file($upfile)) {
        ShowMsg("貌似你什么都没有上传哦！", "javascript:;");
        exit();
    } else {
        if (!preg_match("#\.(htm|html)$#", $upfile_name)) {
            ShowMsg("DedeCMS模板只能用 .htm 或 .html扩展名！", "-1");
            exit();
        }
        if (preg_match("#[\\\\\/]#", $upfile_name)) {
            ShowMsg("模板文件名有非法字符，禁止上传！", "-1");
            exit();
        }
        move_uploaded_file($upfile, $templetdird . '/' . $upfile_name);
        @unlink($upfile);
        ShowMsg("成功上传一个模板！", "templets_main.php?acdir=$acdir");
        exit();
    }
    exit();
}
/*---------------------------
function edittag() { }
修改标签碎片
--------------------------*/
else if ($action == 'edittag' || $action == 'addnewtag') {
    if ($action == 'addnewtag') {
        $democode = '<' . "?php
if(!defined('DEDEINC'))
{
    exit(\"Request Error!\");
}
function lib_demotag(&\$ctag,&\$refObj)
{
    global \$dsql,\$envs;

    //属性处理
    \$attlist=\"row|12,titlelen|24\";
    FillAttsDefault(\$ctag->CAttribute->Items,\$attlist);
    extract(\$ctag->CAttribute->Items, EXTR_SKIP);
    \$revalue = '';

    //你需编写的代码，不能用echo之类语法，把最终返回值传给\$revalue
    //------------------------------------------------------

    \$revalue = 'Hello Word!';

    //------------------------------------------------------
    return \$revalue;
}
";
        $filename = "demotag.lib.php";
        $title = "新建标签";
    } else {
        if (!preg_match("#^[a-z0-9_-]{1,}\.lib\.php$#i", $filename)) {
            ShowMsg('文件不是标准的标签碎片文件，不允许在此编辑！', '-1');
            exit();
        }
        $fp = fopen(DEDEINC . '/taglib/' . $filename, 'r');
        $democode = fread($fp, filesize(DEDEINC . '/taglib/' . $filename));
        fclose($fp);
        $title = "修改标签";
    }
    make_hash();
    $dlist = new DataListCP();
    $dlist->SetTemplet(DEDEADMIN . "/templets/tpl_edit_tag.htm");
    $dlist->display();
    exit();
}
/*---------------------------
function savetagfile() { }
保存标签碎片修改
--------------------------*/
else if ($action == 'savetagfile') {
    csrf_check();
    if (!preg_match("#^[a-z0-9_-]{1,}\.lib\.php$#i", $filename)) {
        ShowMsg('文件名不合法，不允许进行操作！', '-1');
        exit();
    }
    include_once DEDEINC . '/oxwindow.class.php';
    $tagname = preg_replace("#\.lib\.php$#i", "", $filename);
    $content = stripslashes($content);
    $truefile = DEDEINC . '/taglib/' . $filename;
    $fp = fopen($truefile, 'w');
    fwrite($fp, $content);
    fclose($fp);
    $msg = "
    <form name='form1' action='tag_test_action.php' target='blank' method='post'>
      <input type='hidden' name='dopost' value='make' />
      <input type='hidden' name='token' value='{$token}' />
        <b>测试标签：</b>(需要使用环境变量的不能在此测试)<br/>
        <textarea name='partcode' cols='150' rows='6' style='width:90%;'>{dede:{$tagname} }{/dede:{$tagname}}</textarea><br />
        <input name='imageField1' type='image' class='np' src='images/button_ok.gif' width='60' height='22' border='0' />
    </form>
    ";
    $wintitle = "成功修改/创建文件！";
    $wecome_info = "<ul class='uk-breadcrumb'><li><a href='templets_tagsource.php'>标签源码碎片管理</a></li><li><span>修改/新建标签</span></li></ul>";
    $win = new OxWindow();
    $win->AddTitle("修改/新建标签：");
    $win->AddMsgItem($msg);
    $winform = $win->GetWindow("hand", "&nbsp;", false);
    $win->Display();
    exit();
}
