<?php
$hook = array(
    'hook' => 'ClientAdd',
    'function' => 'ClientAdd_admin',
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'Sitenize yeni musteri kayit oldu.',
    'variables' => ''
);
if(!function_exists('ClientAdd_admin')){
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
}
return $hook;