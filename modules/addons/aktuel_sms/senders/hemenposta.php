<?php
class hemenposta extends AktuelSms {

    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){
        $params = $this->getParams();

        $postUrl = "http://sms.modexi.com/service/sendxml";
        $xmlString="<SMS><authentification><username>$params->user</username><password>$params->pass</password></authentification><message><sender>$params->senderid</sender></message><recipients><text>$this->message</text><gsm>$this->gsmnumber</gsm></recipients></SMS>";

        $fields = $xmlString;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        $return = $result;
        $log[] = ("Sunucudan dönen cevap: ".$result);

        if(preg_match('/<status>(.*?)<\/status>(.*?)<DESC>(.*?)<\/DESC>(.*?)<package>(.*?)<\/package>/si', $result, $result_matches)) {
            $status_code = $result_matches[1];
            $status_message = $result_matches[3];
            $order_id = $result_matches[5];

            if($status_code > 0) {
                $log[] = ("Message sent.");
            } else {
                $log[] = ("Mesaj gönderilemedi. Hata: $status_message");
                $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $status_code");
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
    'value' => 'hemenposta',
    'label' => 'HemenPosta',
    'fields' => array(
        'user','pass'
    )
);
