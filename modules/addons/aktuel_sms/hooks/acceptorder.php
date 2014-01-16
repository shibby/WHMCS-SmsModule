<?php
$hook = array(
    'hook' => 'AcceptOrder',
    'function' => 'AcceptOrder_SMS',
    'description' => array(
        'turkish' => 'Sipariş onaylandığında mesaj gönderir',
        'english' => 'After order accepted'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Sayin {firstname} {lastname}, {orderid} numarali siparisiniz onaylanmistir. ',
    'variables' => '{firstname},{lastname},{orderid}'
);
if(!function_exists('AcceptOrder_SMS')){
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

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['orderid']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);


            $class->setGsmnumber($UserInformation['gsmnumber']);
            $class->setUserid($UserInformation['id']);
            $class->setMessage($message);
            $class->send();
        }
    }
}

return $hook;