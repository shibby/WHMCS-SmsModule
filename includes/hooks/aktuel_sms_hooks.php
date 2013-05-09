<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

function getTemplate($name){
    $where = array("name" => array("sqltype" => "LIKE", "value" => $name));
    $result = select_query("mod_aktuelsms_templates", "template,active", $where);
    $data = mysql_fetch_assoc($result);

    if($data['active'] == 0){
        return false;
    }else{
        return $data['template'];
    }
}

function getTemplateExtra($name){
    $where = array("name" => array("sqltype" => "LIKE", "value" => $name));
    $result = select_query("mod_aktuelsms_templates", "extra,active", $where);
    $data = mysql_fetch_assoc($result);

    if($data['active'] == 0){
        return false;
    }else{
        return $data['extra'];
    }
}

function TicketAdminReply($args){

    $template = getTemplate('TicketAdminReply');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id` 
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` IN (SELECT userid FROM tbltickets WHERE id = '".$args['ticketid']."')
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."' 
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);

        $replacefrom = array('{firstname}','{lastname}','{ticketid}','{ticketsubject}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['ticketid'],$args['subject']);
        $Message = str_replace($replacefrom,$replaceto,$template);


        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $UserInformation['id'];
        $send->send();


    }
}

function ClientChangePassword($args){
    $template = getTemplate('ClientChangePassword');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['userid']."'
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $UserInformation['id'];
        $send->send();
    }
}

function ClientAdd($args){
    $template = getTemplate('ClientAdd');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['userid']."'
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{email}','{password}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['email'],$args['password']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $UserInformation['id'];
        $send->send();

    }
}

function AfterRegistrarRegistration($args){
    $template = getTemplate('AfterRegistrarRegistration');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{domain}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $args['params']['userid'];
        $send->send();
    }
}

function AfterRegistrarRenewal($args){
    $template = getTemplate('AfterRegistrarRenewal');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{domain}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $args['params']['userid'];
        $send->send();
    }
}

function AfterRegistrarRegistrationFailed($args){
    $template = getTemplate('AfterRegistrarRegistrationFailed');

    if ($template == false){
        return null;
    }

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{domain}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $args['params']['userid'];
        $send->send();
    }
}

function AfterModuleCreate($args){

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $type = $args['params']['producttype'];

    if($type == "hostingaccount"){
        $template = getTemplate('AfterModuleCreate_SharedAccount');
    }

    if ($template == false){
        return null;
    }

    if($type=="hostingaccount"){
        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `a`.`id`  = '".$args['params']['clientsdetails']['userid']."'
        AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
        AND `c`.`value` = 'on'
        LIMIT 1";
        
    }else{
        return null;
    }

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{domain}','{username}','{password}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain'],$args['params']['username'],$args['params']['password']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $args['params']['clientsdetails']['userid'];
        $send->send();
    }
}

function AcceptOrder_SMS($args){

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $template = getTemplate('AcceptOrder');

    if ($template == false){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `a`.`id` IN (SELECT userid FROM tblorders WHERE id = '".$args['orderid']."')
        AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
        AND `c`.`value` = 'on'
        LIMIT 1";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{orderid}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['orderid']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $UserInformation['id'];
        $send->send();
    }
}

function DomainRenewalNotice($args){

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");

    $template = getTemplate('DomainRenewalNotice');

    if ($template == false){
        return null;
    }

    $extra = getTemplateExtra('DomainRenewalNotice');
    $sqlDomain = "SELECT  `userid` ,  `domain` ,  `expirydate`
           FROM  `tbldomains`
           WHERE  `status` =  'Active'";
    $resultDomain = mysql_query($sqlDomain);
    while ($data = mysql_fetch_array($resultDomain)) {
        $tarih = explode("-",$data['expirydate']);
        $yesterday = mktime (0, 0, 0, $tarih[1], $tarih[2] - $extra, $tarih[0]);
        $today = date("Y-m-d");
        if (date('Y-m-d', $yesterday) == $today){
            $userSql = "SELECT `a`.`id` as userid,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
            FROM `tblclients` as `a`
            JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
            JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
            WHERE `a`.`id` = '".$data['userid']."'
            AND `b`.`fieldid` = '".$Settings['gsmnumberfield']."'
            AND `c`.`fieldid` = '".$Settings['wantsmsfield']."'
            AND `c`.`value` = 'on'
            LIMIT 1";

            $result = mysql_query($userSql);
            $num_rows = mysql_num_rows($result);
            if($num_rows == 1){
                $UserInformation = mysql_fetch_assoc($result);
                $replacefrom = array('{firstname}','{lastname}','{domain}','{x}','{expirydate}');
                $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$data['domain'],$extra,$data['expirydate']);
                $Message = str_replace($replacefrom,$replaceto,$template);

                $send = new SendGsm();
                $send->sender = $Settings['api'];
                $send->params = $Settings['apiparams'];
                $send->gsmnumber = $UserInformation['gsmnumber'];
                $send->message = $Message;
                $send->userid = $UserInformation['userid'];
                $send->send();
            }
        }
    }
}

function InvoicePaymentReminder($args){

    $Settings = mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    if(!$Settings['api'] || !$Settings['apiparams']){
        return null;
    }

    include_once($Settings['path']."modules/admin/aktuel_sms/smsclass.php");


    if($args['type'] == "reminder")
        $template = getTemplate('InvoicePaymentReminder');
    elseif($args['type'] == "firstoverdue")
        $template = getTemplate('InvoicePaymentReminder_FirstOverdue');
    else
        $template = false;

    if ($template == false){
        return null;
    }

    $userSql = "
        SELECT a.duedate,b.id as userid,b.firstname,b.lastname,`c`.`value` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`userid`
        JOIN `tblcustomfieldsvalues` as `d` ON `d`.`relid` = `a`.`userid`
        a.id = '".$args['invoiceid']."'
        AND `c`.`fieldid` = '".$Settings['gsmnumberfield']."'
        AND `d`.`fieldid` = '".$Settings['wantsmsfield']."'
        AND `d`.`value` = 'on'
        LIMIT 1
    ";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = array('{firstname}','{lastname}','{duedate}');
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['duedate']);
        $Message = str_replace($replacefrom,$replaceto,$template);

        $send = new SendGsm();
        $send->sender = $Settings['api'];
        $send->params = $Settings['apiparams'];
        $send->gsmnumber = $UserInformation['gsmnumber'];
        $send->message = $Message;
        $send->userid = $UserInformation['userid'];
        $send->send();
    }
}

add_hook("ClientChangePassword", 1, "ClientChangePassword", "");
add_hook("TicketAdminReply", 1, "TicketAdminReply", "");
add_hook("ClientAdd", 1, "ClientAdd", "");

#Domain
add_hook("AfterRegistrarRegistration", 1, "AfterRegistrarRegistration", "");
add_hook("AfterRegistrarRenewal", 1, "AfterRegistrarRenewal", "");
//add_hook( "AfterRegistrarRegistrationFailed", 1, "AfterRegistrarRegistrationFailed", "");

#Product
add_hook("AfterModuleCreate", 1, "AfterModuleCreate", "");

#Order
add_hook("AcceptOrder", 1, "AcceptOrder_SMS", "");

#Invoice
//add_hook("InvoiceCreationPreEmail", 1, "InvoiceCreationPreEmail", ""); # invoiceid
add_hook("InvoicePaymentReminder", 1, "InvoicePaymentReminder", ""); # invoiceid - type: reminder, firstoverdue, secondoverdue, thirdoverdue

#AktuelSms Cron
add_hook("DailyCronJob", 1, "DomainRenewalNotice", "");
// Product Renewal Notice