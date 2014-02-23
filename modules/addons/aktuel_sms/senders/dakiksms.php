<?php
class dakiksms extends AktuelSms {

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
		'<SMS>'.
			'<oturum>'.
				'<kullanici>'.$params->user.'</kullanici>'.
				'<sifre>'.$params->pass.'</sifre>'.
			'</oturum>'.
			'<mesaj>'.
				'<baslik>'.$params->senderid.'</baslik>'.
				'<metin>'.$this->message.'</metin>'.
				'<alicilar>'.$this->gsmnumber.'</alicilar>'.
			'</mesaj>'.
		'</SMS>';
		$URL = "http://www.dakiksms.com//api/xml_api.php";
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
		$log[] = ("Sunucudan dönen cevap: ".$result);

		$result = explode("|",$result);
		if($result[0]=="OK"){
			$log[] = ("Mesaj Gönderildi.");
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
		$params = $this->getParams();
		if($params->user && $params->pass){
			$xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
			'<RAPOR>'.
				'<oturum>'.
					'<kullanici>'.$params->user.'</kullanici>'.
					'<sifre>'.$params->pass.'</sifre>'.
				'</oturum>'.
			'</RAPOR>';
			
			$URL = "http://www.dakiksms.com/api/xml_bakiye.php"; 
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
			
			if(!$output){
				return null;			
			}else{
				return substr($output, 24, -1);			
			}
		}else{		
			return null;
			}
	}
	
	function report($msgid){
		$params = $this->getParams();
		if($params->user && $params->pass && $msgid){
			$xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
			'<RAPOR>'.
				'<oturum>'.
					'<kullanici>'.$params->user.'</kullanici>'.
					'<sifre>'.$params->pass.'</sifre>'.
				'</oturum>'.
				'<rapor>'.
					'<raporid>'.$msgid.'</raporid>'.
				'</rapor>'.
			'</RAPOR>';
			$URL = "http://www.dakiksms.com/api/xml_rapor.php"; 
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
											
			if($output){
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
	'value' => 'dakiksms',
	'label' => 'Dakik SMS',
	'fields' => array(
	'user','pass'
	)
);
