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
        var field = document.getElementById("customfield'.$field.'");
        field.checked = true;
        var elem = document.getElementById("customfield1");
        elem.value = "My default value";
        </script>';

        return $html;
    }
}

return $hook;