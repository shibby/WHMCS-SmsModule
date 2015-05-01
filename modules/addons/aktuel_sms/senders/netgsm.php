<?php

class netgsm extends AktuelSms {
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

        $url = "http://api.netgsm.com.tr/bulkhttppost.asp?usercode=$params->user&password=$params->pass&gsmno=$this->gsmnumber&message=".urlencode($this->message)."&msgheader=$params->senderid";
        $log[] = "Request url: ".$url;
        $result = file_get_contents($url);

        $return = $result;
        $log[] = "Sunucudan dönen cevap: ".$result;

        $result = explode(" ", $result);
        if ($result[0] == "00" || $result[0] == "01" || $result[0] == "02") {
            $this->addLog("Message sent.");
            $log[] = "Message sent";
            $msgid = $result[1];
            $log[] = "Message id: ".$msgid;
        }elseif($result[0] == "10"){
            $log[] = "Mesaj gönderilemedi. Hata: Telefon numarası hatalı";
            $error[] = "Mesaj gönderilemedi. Hata: Telefon numarası hatalı";
        }elseif($result[0] == "20"){
            $log[] = "Mesaj gönderilemedi. Hata: mesaj metni boş veya çok uzun";
            $error[] = "Mesaj gönderilemedi. Hata: mesaj metni boş veya çok uzun";
        }elseif($result[0] == "30"){
            $log[] = "Mesaj gönderilemedi. Hata: Kullanıcı bilgisi bulunamadı";
            $error[] = "Mesaj gönderilemedi. Hata: Kullanıcı bilgisi bulunamadı";
        }elseif($result[0] == "40"){
            $log[] = "Mesaj gönderilemedi. Hata: Geçersiz mesaj başlığı";
            $error[] = "Mesaj gönderilemedi. Hata: Geçersiz mesaj başlığı";
        }elseif($result[0] == "50"){
            $log[] = "Mesaj gönderilemedi. Hata: Kullanıcının kredisi yok";
            $error[] = "Mesaj gönderilemedi. Hata: Kullanıcının kredisi yok";
        }elseif($result[0] == "60"){
            $log[] = "Mesaj gönderilemedi. Hata: Telefon numarası hiç tanımlanmamış";
            $error[] = "Mesaj gönderilemedi. Hata: Telefon numarası hiç tanımlanmamış";
        }elseif($result[0] == "70"){
            $log[] = "Mesaj gönderilemedi. Hata: Mesaj başlığı hatalı";
            $error[] = "Mesaj gönderilemedi. Hata: Mesaj başlığı hatalı";
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
        $params = $this->getParams();

        if($params->user && $params->pass){
            $postUrl = "https://api.netgsm.com.tr/xmlpaketkampanya.asp";
            $xmlString="<mainbody><header><company>Netgsm</company><usercode>$params->user</usercode><password>$params->pass</password><stip>1</stip></header></mainbody>";
            // Kalan SMS Adedi Sorgula
            $fields = $xmlString;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $postUrl);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            curl_close($ch);
            
            $url = "http://api.netgsm.com.tr/get_kredi.asp?usercode=$params->user&password=$params->pass";
            // Kalan TL Miktarı Sorgula
            $result2 = file_get_contents($url);
            $result2 = explode(" ",$result2);
            
            $result = str_replace("|", " ", $result);
            $result = str_replace("<BR>", " ", $result);
            $result = "$result | $result2[1] TL";
                
            return $result;
        }else{
            return null;
        }
    }

    function report($msgid){
        $params = $this->getParams();

        if($params->user && $params->pass && $msgid){
            $url = "http://api.netgsm.com.tr/httpbulkrapor.asp?usercode=$params->user&password=$params->pass&bulkid=$msgid&type=0&status=";
            //status değiştiriliyor
            $url1 = $url."1";
            $result = file_get_contents($url1);
            if($result != "30" && $result != "60"){
                return "success";
            }else{
                return "error";
            }
        }else{
            return null;
        }
    }

    function utilgsmnumber($number){
        if (strlen($number) == 10) {
            $number = '90' . $number;
        } elseif (strlen($number) == 11) {
            $number = '9' . $number;
        }

        if (substr($number, 0, 3) != "905") {
            return "error";
        }

        return $number;
    }

    //You can spesifically convert your message
    function utilmessage($message){
        $changefrom = array('ı', 'İ', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ', 'ç', 'Ç','ş','Ş');
        $changeto = array('i', 'I', 'u', 'U', 'o', 'O', 'g', 'G', 'c', 'C','s','S');
        $message = str_replace($changefrom, $changeto, $message);
        return $message;
    }
}

return array(
    'value' => 'netgsm',
    'label' => 'NetGsm',
    'fields' => array(
        'user','pass'
    )
);
