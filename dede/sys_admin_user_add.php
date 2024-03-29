<?php
/**
 * 添加系统管理员
 *
 * @version   $Id: sys_admin_user_add.php 1 16:22 2010年7月20日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https://weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
CheckPurview('sys_User');
require_once DEDEINC . "/typelink.class.php";
if (empty($dopost)) {
    $dopost = '';
}

if ($dopost == 'add') {
    csrf_check();
    if (preg_match("#[^0-9a-zA-Z_@!\.-]#", $pwd) || preg_match("#[^0-9a-zA-Z_@!\.-]#", $userid)) {
        ShowMsg('密码或或用户名不合法，<br />请使用[0-9a-zA-Z_@!.-]内的字符！', '-1', 0, 3000);
        exit();
    }
    $safecodeok = substr(md5($cfg_cookie_encode . $randcode), 0, 24);
    if ($safecode != $safecodeok) {
        ShowMsg('请填写安全验证串！', '-1', 0, 3000);
        exit();
    }
    $row = $dsql->GetOne("SELECT COUNT(*) AS dd FROM `#@__member` WHERE userid LIKE '$userid' ");
    if ($row['dd'] > 0) {
        ShowMsg('用户名已存在！', '-1');
        exit();
    }
    $mpwd = md5($pwd);
    $pwd = substr(md5($pwd), 5, 20);

    $typeid = join(',', $typeids);
    if ($typeid == '0') {
        $typeid = '';
    }

    //关连前台用户帐号
    $adminquery = "INSERT INTO `#@__member` (`mtype`,`userid`,`pwd`,`uname`,`sex`,`rank`,`money`,`email`,
                   `scores` ,`matt` ,`face`,`safequestion`,`safeanswer` ,`jointime` ,`joinip` ,`logintime` ,`loginip` )
               VALUES ('个人','$userid','$mpwd','$uname','男','100','0','$email','1000','10','','0','','0','','0',''); ";
    $dsql->ExecuteNoneQuery($adminquery);

    $mid = $dsql->GetLastID();
    if ($mid <= 0) {
        die($dsql->GetError() . ' 数据库出错！');
    }

    //后台管理员
    $inquery = "INSERT INTO `#@__admin`(id,usertype,userid,pwd,uname,typeid,tname,email)
                                                    VALUES('$mid','$usertype','$userid','$pwd','$uname','$typeid','$tname','$email'); ";
    $rs = $dsql->ExecuteNoneQuery($inquery);

    $adminquery = "INSERT INTO `#@__member_person` (`mid`,`onlynet`,`sex`,`uname`,`qq`,`msn`,`tel`,`mobile`,`place`,`oldplace`,`birthday`,`star`,
                   `income` , `education` , `height` , `bodytype` , `blood` , `vocation` , `smoke` , `marital` , `house` ,`drink` , `datingtype` , `language` , `nature` , `lovemsg` , `address`,`uptime`)
                VALUES ('$mid', '1', '男', '{$userid}', '', '', '', '', '0', '0','1980-01-01', '1', '0', '0', '160', '0', '0', '0', '0', '0', '0','0', '0', '', '', '', '','0'); ";
    $dsql->ExecuteNoneQuery($adminquery);

    $adminquery = "INSERT INTO `#@__member_tj` (`mid`,`article`,`album`,`archives`,`homecount`,`pagecount`,`feedback`,`friend`,`stow`)
                     VALUES ('$mid','0','0','0','0','0','0','0','0'); ";
    $dsql->ExecuteNoneQuery($adminquery);

    $adminquery = "Insert Into `#@__member_space`(`mid` ,`pagesize` ,`matt` ,`spacename` ,`spacelogo` ,`spacestyle`, `sign` ,`spacenews`)
                Values('$mid','10','0','{$uname}的空间','','person','',''); ";
    $dsql->ExecuteNoneQuery($adminquery);

    ShowMsg('成功增加一个用户！', 'sys_admin_user.php');
    exit();
}
$randcode = mt_rand(10000, 99999);
$safecode = substr(md5($cfg_cookie_encode . $randcode), 0, 24);

$dsql->SetQuery("SELECT reid,id,typename FROM `#@__arctype` order by topid  asc , sortrank asc");
$dsql->Execute('op');
while ($item = $dsql->GetArray('op')) {
    $rows[] = $item;
}
$typeOptions = array();
$index = array();

foreach($rows as $value) {  
    if($value["reid"] == 0) { 
        $typeOptions[$value["id"]] = $value;  
        $index[$value["id"]] =& $typeOptions[$value["id"]];
    }else {  
        $index[$value["reid"]][$value["id"]] = $value;  
        $index[$value["id"]] =& $index[$value["reid"]][$value["id"]];  
    }
}

function getswitch($data, $l)
{
    foreach($data as $key=>$value){
        if(is_array($value)) {
            $result=getswitch($value, $l);
        }
        else{
            $result[$key]=$value;
            if (count($result) == 3) {
                $l++;
                $line = "";
                for ($i=0; $i < $l-1; $i++) { 
                    $line .= "—";
                }
                echo "<option value='{$result["id"]}'>{$line} {$result["typename"]}</option>\r\n";
            }
        }
    }
    return $result;
} 
make_hash();
DedeInclude('templets/sys_admin_user_add.htm');
