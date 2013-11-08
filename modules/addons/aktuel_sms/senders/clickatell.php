<?php
class clickatell extends AktuelSms {

    function __construct($message,$gsmnumber){
        $this->message = $message;
        $this->gsmnumber = $gsmnumber;
    }

    function send(){
        $params = $this->getParams();

        $baseurl = "http://api.clickatell.com";

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

        $url = "$baseurl/http/auth?user=$params->user&password=$params->pass&api_id=$params->apiid&from=$params->senderid";
        $ret = file($url);
        $log[] = ("Sunucudan dönen cevap: ".$ret);

        $sess = explode(":", $ret[0]);
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=$params->senderid";

            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                $log[] = ("Mesaj gönderildi.");
            } else {
                $log[] = ("Mesaj gönderilemedi. Hata: $ret");
                $error[] = ("Mesaj gönderilirken hata oluştu. Hata: $ret");
            }
        } else {
            $log[] = ("Mesaj gönderilemedi. Authentication Hata: $ret[0]");
            $error[] = ("Authentication failed. $ret[0] ");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $send[1],
        );
    }

    function balance(){
		$params = $this->getParams();
        if($params->user && $params->pass &&$params->apiid){
			$url = 	"http://api.clickatell.com/http/getbalance?api_id=$params->apiid&user=$params->user&password=$params->pass";
            $result = file_get_contents($url);
            $result = explode(" ",$result);
			$cvp = $result[1];
            $h0 = 001;
            $h1 = 002;
            if($cvp == $h0){
//                return ("Kimlik doğrulama bilgileri hatalı.Hata Kodu: $cvp");
                return null;
            }elseif($cvp == $h1){
//              return ("Yetkilendirme hatası, bilinmeyen kullanıcı adı veya hatalı parola. Hata Kodu: $cvp");
                return null;
            }else{
                return $cvp;
            }
        }else{
            return null;
        }
    }

    function report($msgid){
        return null;
    }
}

return array(
    'value' => 'clickatell',
    'label' => 'ClickAtell',
    'fields' => array(
        'user','pass','apiid'
    )
);
