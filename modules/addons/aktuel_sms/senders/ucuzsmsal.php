<?php

class ucuzsmsal extends AktuelSms {
    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){
        $params = json_decode($this->params);

        $url = "http://www.ucuzsmsal.com/api/index.php?act=sendsms&user=".$params->user."&pass=".$params->pass."&orgin=".$params->senderid."&message=".urlencode($this->message)."&numbers=$this->gsmnumber";

        $result = file_get_contents($url);
        $return = $result;
        $log[] = ("Sunucudan dönen cevap: ".$result);

        $result = explode("|",$result);
        if($result[0]=="OK"){
            $log[] = ("Message sent.");
        }else{
            $log[] = ("Mesaj gönderilemedi. Hata: $return");
            $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $return");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $result[1],
        );
    }
    function balance(){
        return null;
    }
    function report($msgid){
        return null;
    }
}

return array(
    'value' => 'ucuzsmsal',
    'label' => 'Ucuz Sms Al',
    'fields' => array(
        'user','pass'
    )
);