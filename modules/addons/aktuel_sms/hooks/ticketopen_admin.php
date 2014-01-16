<?php
$hook = array(
    'hook' => 'TicketOpen',
    'function' => 'TicketOpen_admin',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'Yeni bir ticket acildi. ({subject})',
    'variables' => '{subject}'
);

if(!function_exists('TicketOpen_admin')){
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

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($args['subject']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $class->setGsmnumber(trim($gsm));
                $class->setUserid(0);
                $class->setMessage($message);
                $class->send();
            }
        }
    }
}

return $hook;
