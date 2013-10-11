<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

$path = str_replace('includes/hooks','modules/admin/aktuel_sms',dirname(__FILE__));
include($path."/smsclass.php");

function TicketAdminReply($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);

    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` IN (SELECT userid FROM tbltickets WHERE id = '".$args['ticketid']."')
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);

        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['ticketid'],$args['subject']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['id'];
        $class->send();
    }
}

function ClientChangePassword($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['userid']."'
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);
        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['id'];
        $class->send();
    }
}

function ClientAdd($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['userid']."'
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    $UserInformation = mysql_fetch_assoc($result);

    if($num_rows == 1){
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['email'],$args['password']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);
        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['id'];
        $class->send();
    }
}

function AfterRegistrarRegistration($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['userid'];
        $class->send();
    }

}

function AfterRegistrarRenewal($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['userid'];
        $class->send();
    }

    /* Admin section */
    $data = array(
        'domain' => $args['params']['sld'].".".$args['params']['tld']
    );
    sendToAdmin('AfterRegistrarRenewal_admin',$data);
    /* Admin section */
}

function AfterRegistrarRegistrationFailed($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `a`.`id` = '".$args['params']['userid']."'
	AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'
	LIMIT 1";
    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['userid'];
        $class->send();
    }
}

function AfterModuleCreate_Hosting($args){

    $type = $args['params']['producttype'];

    if($type == "hostingaccount"){
        $class = new AktuelSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
    }else{
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
    FROM `tblclients` as `a`
    JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
    JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
    WHERE `a`.`id`  = '".$args['params']['clientsdetails']['userid']."'
    AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
    AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
    AND `c`.`value` = 'on'
    LIMIT 1";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain'],$args['params']['username'],$args['params']['password']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['clientsdetails']['userid'];
        $class->send();
    }
}

function AfterModuleSuspend($args){

    $type = $args['params']['producttype'];

    if($type == "hostingaccount"){
        $class = new AktuelSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
    }else{
        return null;
    }


    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
    FROM `tblclients` as `a`
    JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
    JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
    WHERE `a`.`id`  = '".$args['params']['clientsdetails']['userid']."'
    AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
    AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
    AND `c`.`value` = 'on'
    LIMIT 1";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['clientsdetails']['userid'];
        $class->send();
    }
}
function AfterModuleUnsuspend($args){

    $type = $args['params']['producttype'];

    if($type == "hostingaccount"){
        $class = new AktuelSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
    }else{
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
    FROM `tblclients` as `a`
    JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
    JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
    WHERE `a`.`id`  = '".$args['params']['clientsdetails']['userid']."'
    AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
    AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
    AND `c`.`value` = 'on'
    LIMIT 1";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);
        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $args['params']['clientsdetails']['userid'];
        $class->send();
    }
}

function AcceptOrder_SMS($args){

    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
        FROM `tblclients` as `a`
        JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
        WHERE `a`.`id` IN (SELECT userid FROM tblorders WHERE id = '".$args['orderid']."')
        AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `c`.`value` = 'on'
        LIMIT 1";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['orderid']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['id'];
        $class->send();
    }
}

function DomainRenewalNotice($args){

    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $extra = $template['extra'];
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
            AND `b`.`fieldid` = '".$settings['gsmnumberfield']."'
            AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
            AND `c`.`value` = 'on'
            LIMIT 1";

            $result = mysql_query($userSql);
            $num_rows = mysql_num_rows($result);
            if($num_rows == 1){
                $UserInformation = mysql_fetch_assoc($result);
                $replacefrom = explode(",",$template['variables']);
                $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$data['domain'],$data['expirydate'],$extra);
                $message = str_replace($replacefrom,$replaceto,$template['template']);

                $class->sender = $settings['api'];
                $class->params = $settings['apiparams'];
                $class->gsmnumber = $UserInformation['gsmnumber'];
                $class->message = $message;
                $class->userid = $UserInformation['userid'];
                $class->send();
            }
        }
    }
}

function InvoiceCreated($args){

    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }

    $userSql = "
        SELECT a.duedate,b.id as userid,b.firstname,b.lastname,`c`.`value` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`userid`
        JOIN `tblcustomfieldsvalues` as `d` ON `d`.`relid` = `a`.`userid`
        WHERE a.id = '".$args['invoiceid']."'
        AND `c`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `d`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `d`.`value` = 'on'
        LIMIT 1
    ";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['duedate']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);
        
        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['userid'];
        $class->send();
    }
}

function InvoicePaymentReminder_Reminder($args){

    if($args['type'] == "reminder"){
        $class = new AktuelSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
    }else{
        return false;
    }

    $userSql = "
        SELECT a.duedate,b.id as userid,b.firstname,b.lastname,`c`.`value` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`userid`
        JOIN `tblcustomfieldsvalues` as `d` ON `d`.`relid` = `a`.`userid`
        WHERE a.id = '".$args['invoiceid']."'
        AND `c`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `d`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `d`.`value` = 'on'
        LIMIT 1
    ";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['duedate']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['userid'];
        $class->send();
    }
}

function InvoicePaymentReminder_Firstoverdue($args){

    if($args['type'] == "firstoverdue"){
        $class = new AktuelSms();
        $template = $class->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $class->getSettings();
        if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
            return null;
        }
    }else{
        return false;
    }

    $userSql = "
        SELECT a.duedate,b.id as userid,b.firstname,b.lastname,`c`.`value` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`userid`
        JOIN `tblcustomfieldsvalues` as `d` ON `d`.`relid` = `a`.`userid`
        WHERE a.id = '".$args['invoiceid']."'
        AND `c`.`fieldid` = '".$settings['gsmnumberfield']."'
        AND `d`.`fieldid` = '".$settings['wantsmsfield']."'
        AND `d`.`value` = 'on'
        LIMIT 1
    ";

    $result = mysql_query($userSql);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$UserInformation['duedate']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $class->sender = $settings['api'];
        $class->params = $settings['apiparams'];
        $class->gsmnumber = $UserInformation['gsmnumber'];
        $class->message = $message;
        $class->userid = $UserInformation['userid'];
        $class->send();
    }
}

function ClientAdd_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $template['template'];
            $class->userid = 0;
            $class->send();
        }
    }
}

function AfterRegistrarRegistration_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}

function AfterRegistrarRenewal_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}
function AfterRegistrarRegistrationFailed_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}
function AfterRegistrarRenewalFailed_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}
function TicketOpen_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['subject']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}
function TicketUserReply_admin($args){
    $class = new AktuelSms();
    $template = $class->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $class->getSettings();
    if(!$settings['api'] || !$settings['apiparams'] || !$settings['gsmnumberfield'] || !$settings['wantsmsfield']){
        return null;
    }
    $admingsm = explode(",",$template['admingsm']);

    $replacefrom = explode(",",$template['variables']);
    $replaceto = array($args['subject']);
    $message = str_replace($replacefrom,$replaceto,$template['template']);

    foreach($admingsm as $gsm){
        if(!empty($gsm)){
            $class->sender = $settings['api'];
            $class->params = $settings['apiparams'];
            $class->gsmnumber = trim($gsm);
            $class->message = $message;
            $class->userid = 0;
            $class->send();
        }
    }
}

$class = new AktuelSms();
$hooks = $class->getHooks();

foreach($hooks as $hook){
    add_hook($hook['hook'], 1, $hook['function'], "");
}