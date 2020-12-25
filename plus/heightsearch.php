<?php
/**
 * 高级搜索
 *
 * @version   $Id: heightsearch.php 1 15:38 2010年7月8日 $
 * @package   DedeCMS.Site
 * @founder   IT柏拉图, https: //weibo.com/itprato
 * @author    DedeCMS团队
 * @copyright Copyright (c) 2007 - 2020, 上海卓卓网络科技有限公司 (DesDev, Inc.)
 * @license   http://help.dedecms.com/usersguide/license.html
 * @link      http://www.dedecms.com
 */
require_once dirname(__FILE__) . '/../include/common.inc.php';
require_once DEDEINC . '/typelink.class.php';
$dlist = new DataListCP();
$dlist->SetTemplate(DEDETEMPLATE . '/plus/heightsearch.htm');
$dlist->Display();
