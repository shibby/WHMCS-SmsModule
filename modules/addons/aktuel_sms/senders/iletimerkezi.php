<?php

class iletimerkezi extends AktuelSms {
    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){

        $params = $this->getParams();

        $url = "http://api.iletimerkezi.com/v1/send-sms/get/?username=$params->user&password=$params->pass&receipents=$this->gsmnumber&text=".urlencode($this->message)."&sender=".urlencode($params->senderid);

        $result = file_get_contents($url);
        $return = $result;
        $log[] = ("Sunucudan dönen cevap: ".$result);

        if(preg_match('/<status>(.*?)<code>(.*?)<\/code>(.*?)<message>(.*?)<\/message>(.*?)<\/status>(.*?)<order>(.*?)<id>(.*?)<\/id>(.*?)<\/order>/si', $result, $result_matches)) {
            $status_code = $result_matches[2];
            $status_message = $result_matches[4];
            $order_id = $result_matches[8];

            if($status_code == '200') {
                $log[] = ("Message sent.");
            } else {
                $log[] = ("Mesaj gönderilemedi. Hata: $status_message");
                $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $status_message");
            }
        } else {
            $log[] = ("Mesaj gönderilemedi. Hata: $return");
            $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $return");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $order_id,
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
    'value' => 'iletimerkezi',
    'label' => 'İleti Merkezi',
    'fields' => array(
        'user','pass'
    )
);
