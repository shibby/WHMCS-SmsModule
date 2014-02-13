<?php
//ClientAreaHeadOutput
$hook = array(
    'hook' => 'ClientAreaHeadOutput',
    'function' => 'ClientAreaHeadOutput',
);
if(!function_exists('ClientAreaHeadOutput')){
    function ClientAreaHeadOutput($args){
        $class = new AktuelSms();
        $settings = $class->getSettings();
        $field = $settings['wantsmsfield'];

        $html = '<script type="text/javascript">
        //$("#customfield'.$field.'").attr("checked","checked");
        $("#customfield'.$field.'").each(function(){ this.checked = true; });
        </script>';

        return $html;
    }
}

return $hook;