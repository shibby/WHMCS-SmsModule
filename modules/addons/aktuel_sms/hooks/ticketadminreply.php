<?php
$hook = array(
    'hook' => 'TicketAdminReply',
    'function' => 'TicketAdminReply',
    'description' => array(
        'turkish' => 'Bir ticket güncellendiğinde mesaj gönderir',
        'english' => 'After ticket replied by admin'
    ),
    'type' => 'client',
    'extra' => '',
    'variables' => '{firstname},{lastname},{ticketsubject}',
    'defaultmessage' => 'Sayin {firstname} {lastname}, ({ticketsubject}) konu baslikli destek talebiniz yanitlandi.',
);

if(!function_exists('TicketAdminReply')){
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
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['subject']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);
            $class->setGsmnumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['id']);
            $class->send();
        }
    }
}

return $hook;
