<?php

class ucuzsmsal extends AktuelSms {
    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
    }

    function send(){
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }
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

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        if (strlen($number) == 10) {

        } elseif (strlen($number) == 11) {
            $number = substr($number,1,strlen($number));
        } elseif (strlen($number) == 12) {
            $number = substr($number,2,strlen($number));
        }

        if (substr($number, 0, 1) != "5") {
            return "numbererror";
        }
        return $number;
    }

    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'ucuzsmsal',
    'label' => 'Ucuz Sms Al',
    'fields' => array(
        'user','pass'
    )
);