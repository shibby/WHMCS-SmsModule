<?php

class routesms extends AktuelSms {
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
        $params = $this->getParams();

        $url = "http://121.241.242.114:8080/bulksms/bulksms?username=$params->user&password=$params->pass&type=1&dlr=0&destination=$this->gsmnumber&source=$params->senderid&message=".urlencode($this->message)."";
        $log[] = "Request url: ".$url;
        $result = file_get_contents($url);

        $return = $result;
        $log[] = "Sunucudan dönen cevap: ".$result;

        $result = explode("|", $result);
        if ($result[0] == "1701") {
            $this->addLog("Message sent.");
            $log[] = "Message sent";
            $msgid = $result[2];
            $log[] = "Message id: ".$msgid;
        }elseif($result[0] == "1702"){
            $log[] = "Invalid URL Error, one of the parameters was not provided or left blank";
            $error[] = "Invalid URL Error, one of the parameters was not provided or left blank";
        }elseif($result[0] == "1703"){
            $log[] = "Invalid value in username or password field ";
            $error[] = "Invalid value in username or password field ";
        }elseif($result[0] == "1704"){
            $log[] = "Invalid value in type field ";
            $error[] = "Invalid value in type field ";
        }elseif($result[0] == "1705"){
            $log[] = "Invalid message";
            $error[] = "Invalid message";
        }elseif($result[0] == "1706"){
            $log[] = "Invalid Destination ";
            $error[] = "Invalid Destination ";
        }elseif($result[0] == "1707"){
            $log[] = "Invalid Source (Sender) ";
            $error[] = ":Invalid Source (Sender) ";
        }elseif($result[0] == "1708"){
            $log[] = "Invalid value for dlr field ";
            $error[] = "Invalid value for dlr field ";
        }elseif($result[0] == "1709"){
            $log[] = "User validation failed ";
            $error[] = "User validation failed ";
        }elseif($result[0] == "1710"){
            $log[] = "Internal Error ";
            $error[] = "Internal Error ";
        }elseif($result[0] == "1025"){
            $log[] = "Insufficient Credit ";
            $error[] = "Insufficient Credit ";
        }else{
            $log[] = "Mesaj gönderilemedi. Hata: $return";
            $error[] = "Mesaj gönderilirken hata oluştu. Hata: $return";
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $msgid,
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
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'routesms',
    'label' => 'Route Sms',
    'fields' => array(
        'user','pass'
    )
);