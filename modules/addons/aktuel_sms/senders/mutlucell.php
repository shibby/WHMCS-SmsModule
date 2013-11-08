<?php
class mutlucell extends AktuelSms {

    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){
        $params = $this->getParams();
        
        $xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
            '<smspack ka="'.$params->user.'" pwd="'.$params->pass.'" org="'.$params->senderid.'" >'.
            '<mesaj>'.
            '<metin>'.$this->message.'</metin>'.
            '<nums>'.$this->gsmnumber.'</nums>'.
            '</mesaj>'.
            '</smspack>';
        $URL = "https://smsgw.mutlucell.com/smsgw-ws/sndblkex";
        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);
        $return = $result;
        $log[] = ("Geri Dönüş Kodu: ".$result);
        $h0 = 20;
        $h1 = 21;
        $h2 = 22;
        $h3 = 23;
        $h4 = 24;
        $h5 = 25;
        $h6 = 30;
        if($return == $h0):
            $log[]= ("Post edilen xml eksik veya hatalı.Hata Kodu: $return");
            $error[] = ("Post edilen xml eksik veya hatalı.Hata Kodu: $return");
        elseif($return == $h1):
            $log[] = ("Kullanılan originatöre sahip değilsiniz.Hata Kodu: $return");
            $error[] = ("Kullanılan originatöre sahip değilsiniz.Hata Kodu: $return");
        elseif($return == $h2):
            $log[] = ("Kontörünüz yetersiz.Hata Kodu: $return");
            $error[] = ("Kontörünüz yetersiz.Hata Kodu: $return");
        elseif($return == $h3):
            $log[] = ("Kullanıcı adı ya da parolanız hatalı. Hata Kodu: $return");
            $error[] = ("Kullanıcı adı ya da parolanız hatalı. Hata Kodu: $return");
        elseif($return == $h4):
            $log[] = ("Şu anda size ait başka bir işlem aktif.Hata Kodu: $return");
            $error[] = ("Şu anda size ait başka bir işlem aktif.Hata Kodu: $return");
        elseif($return == $h5):
            $log[] = ("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.Hata Kodu: $return");
            $error[] = ("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.Hata Kodu: $return");
        elseif($return == $h6):
            $log[] = ("Hesap Aktivasyonu sağlanmamış.Hata Kodu: $return");
            $error[] = ("Hesap Aktivasyonu sağlanmamış.Hata Kodu: $return");
        else:
            $log[] = ("Mesaj Başarıyla Gönderildi.");
        endif;

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $result,
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
    'value' => 'mutlucell',
    'label' => 'MutluCell',
    'fields' => array(
        'user','pass'
    )
);
