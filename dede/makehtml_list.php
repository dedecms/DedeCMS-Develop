<?php
/**
 * 生成列表栏目
 *
 * @version   $Id: makehtml_list.php 1 11:09 2010年7月19日 $
 * @package   DedeCMS.Administrator
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2021, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . "/config.php";
require_once DEDEINC . "/typelink.class.php";
$tpl = new DedeTemplate();
$tpl->LoadTemplate(DEDEADMIN . "/templets/makehtml_list.htm");
$tpl->Display();
