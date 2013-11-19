<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");

require_once("smsclass.php");
$class = new AktuelSms();
$hooks = $class->getHooks();

foreach($hooks as $hook){
    add_hook($hook['hook'], 1, $hook['function'], "");
}