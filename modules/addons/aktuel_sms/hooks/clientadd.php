<?php
$hook = array(
    'hook' => 'ClientAdd',
    'function' => 'ClientAdd',
    'description' => array(
        'turkish' => 'Müşteri kayıt olduktan sonra mesaj gönderir',
        'english' => 'After client register'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Sayin {firstname} {lastname}, AktuelHost a kayit oldugunuz icin tesekkur ederiz. Email: {email} Sifre: {password}',
    'variables' => '{firstname},{lastname},{email},{password}'
);
if(!function_exists('ClientAdd')){
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
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['email'],$args['password']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);

            $class->setGsmnumber($UserInformation['gsmnumber']);
            $class->setMessage($message);
            $class->setUserid($UserInformation['id']);
            $class->send();
        }
    }
}

return $hook;