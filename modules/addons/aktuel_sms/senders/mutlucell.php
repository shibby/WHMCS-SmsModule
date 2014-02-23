<?php

class mutlucell extends AktuelSms {

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
		
        $result = explode(" ", $result);
		if($result[0] == 20){
			$log[]= ("Post edilen xml eksik veya hatalı.");
            $error[] = ("Post edilen xml eksik veya hatalı.");
		}elseif($result[0] == 21){
			$log[] = ("Kullanılan originatöre sahip değilsiniz.");
            $error[] = ("Kullanılan originatöre sahip değilsiniz.");
		}elseif($result[0] == 22){
            $log[] = ("Kontörünüz yetersiz.");
            $error[] = ("Kontörünüz yetersiz.");			
		}elseif($result[0] == 23){
            $log[] = ("Kullanıcı adı ya da parolanız hatalı.");
            $error[] = ("Kullanıcı adı ya da parolanız hatalı.");			
		}elseif($result[0] == 24){
			$log[] = ("Şu anda size ait başka bir işlem aktif.");
            $error[] = ("Şu anda size ait başka bir işlem aktif.");
		}elseif($result[0] == 25){
            $log[] = ("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.");
            $error[] = ("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.");			
		}elseif($result[0] == 30){
            $log[] = ("Hesap Aktivasyonu sağlanmamış.");
            $error[] = ("Hesap Aktivasyonu sağlanmamış.");			
		}else{
			$log[] = ("Mesaj Başarıyla Gönderildi.");			
		}			

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => substr($result[0], 1, -4),
        );
    }
    function balance(){
		$params = $this->getParams();
		if($params->user && $params->pass){
			$xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
			'<smskredi ka="'.$params->user.'" pwd="'.$params->pass.'" />';
			$URL = "https://smsgw.mutlucell.com/smsgw-ws/gtcrdtex"; 
            $ch = curl_init($URL);
            curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);
			
			$result = explode(" ", $output);
			if($result[0] == 20){
				return null;
			}elseif($result[0] == 23){
				return null;
			}else{
				return substr($output, 1, -2);
			}
		}else{		
        	return null;
		}
    }

    function report($msgid){
		$params = $this->getParams();
        if($params->user && $params->pass && $msgid){
			$xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
			'<smsrapor ka="'.$params->user.'" pwd="'.$params->pass.'" id="'.$msgid.'" />';
			$URL = "https://smsgw.mutlucell.com/smsgw-ws/gtblkrprtex"; 
            $ch = curl_init($URL);
            curl_setopt($ch, CURLOPT_MUTE, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($ch);
            curl_close($ch);								
            if($output != 20 && $output != 23 && $output != 30){
                return "success";
            }else{
                return "error";
            }			
		}else{		
        	return null;			
		}
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
    'value' => 'mutlucell',
    'label' => 'MutluCell',
    'fields' => array(
        'user','pass'
    )
);

