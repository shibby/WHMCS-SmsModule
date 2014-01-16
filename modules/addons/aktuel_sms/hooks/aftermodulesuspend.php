<?php
$hook = array(
    'hook' => 'AfterModuleSuspend',
    'function' => 'AfterModuleSuspend',
    'description' => array(
        'turkish' => 'Bir hizmet duraklatıldığında mesaj gönderir',
        'english' => 'After module suspended'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Sayin {firstname} {lastname}, hizmetiniz duraklatildi. ({domain})',
    'variables' => '{firstname},{lastname},{domain}'
);
if(!function_exists('AfterModuleSuspend')){
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

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);

            $class->setGsmnumber($UserInformation['gsmnumber']);
            $class->setUserid($args['params']['clientsdetails']['userid']);
            $class->setMessage($message);
            $class->send();
        }
    }
}

return $hook;