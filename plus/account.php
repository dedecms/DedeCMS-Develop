<?php
/**
 * @version   $Id: account.php 1 8:38 2010年7月9日 $
 * @package   DedeCMS.Member
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/users_config.php";
$gourl = RemoveXSS($gourl);
$cfg_ml = new MemberLogin();

// 用户状态
if ($dopost === 'status') {
    $tpl = DEDETEMPLATE . '/plus/'."users-status-notlogged.htm";

    if ($cfg_ml->IsLogin()){
        $tpl = DEDETEMPLATE . '/plus/'."users-status-logged.htm";
    }  

    $dlist = new DataListCP();
    $dlist->SetTemplate($tpl);
    $dlist->Display();
} else if ($dopost === 'carnum') {
    require_once DEDEINC . "/shopcar.class.php";
    $cart = new MemberShops();
    echo $cart->cartCount();
}
