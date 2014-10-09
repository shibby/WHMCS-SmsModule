<?php

class birsms extends AktuelSms {

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
		'<Submit xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="SmsApi">'.
        '<Credential>'.
        	'<Password>'.$params->pass.'</Password>'.
        	'<Username>'.$params->user.'</Username>'.
        '</Credential>'.
        '<DataCoding>Default</DataCoding>'.
        '<Header>'.
        	 '<From>'.$params->senderid.'</From>'.
       '</Header>'.
        '<Message>'.$this->message.'</Message>'.
		'<To xmlns:d2p1="http://schemas.microsoft.com/2003/10/Serialization/Arrays">'.
            '<d2p1:string>'.$this->gsmnumber.'</d2p1:string>'.
		'</To>'.
		'</Submit>';
		
        $URL = "http://api.1sms.com.tr/v1/xml/syncreply/Submit";
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
        
        $simple = XMLToArray($result);
        $cvp = $simple["SUBMITRESPONSE"]["RESPONSE"]["STATUS"]["CODE"];
		$msgID = $simple["SUBMITRESPONSE"]["RESPONSE"]["MESSAGEID"];
		
        $log[] = ("Geri Dönüş Kodu: ".$cvp);
		
		if($cvp != 200){
			$log[]= ("Hata..Mesaj Gönderilemedi.");
            $error[] = ("Hata..Mesaj Gönderilemedi.");			
		}else{
			$log[] = ("Mesaj Başarıyla Gönderildi.");			
		}
		
        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $msgID,
        );
    }
    function balance(){
		$params = $this->getParams();
		if($params->user && $params->pass){
			$xml_data =
			'<GetBalance xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="SmsApi">'.
    			'<Credential>'.
					'<Password>'.$params->pass.'</Password>'.
					'<Username>'.$params->user.'</Username>'.
				'</Credential>'.
			'</GetBalance>';
			
			$URL = "http://api.1sms.com.tr/v1/xml/syncreply/GetBalance"; 
			
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
			
			$simple = XMLToArray($output);
			return substr($simple["GETBALANCERESPONSE"]["RESPONSE"]["BALANCE"]["MAIN"], 0, -5);
		}else{		
        	return null;
		}
    }

    function report($msgid){
		$params = $this->getParams();
        if($params->user && $params->pass && $msgid){
			$xml_data =
			'<Query xmlns:i="http://www.w3.org/2001/XMLSchema-instance" xmlns="SmsApi">'.
			'<Credential>'.
				'<Password>'.$params->pass.'</Password>'.
				'<Username>'.$params->user.'</Username>'.
			'</Credential>'.
			'<MessageId>'.$msgid.'</MessageId>'.
			'</Query>';
			
			$URL = "http://api.1sms.com.tr/v1/xml/syncreply/Query"; 
			
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
            $simple = XMLToArray($output);
			$cvp = $simple["QUERYRESPONSE"]["RESPONSE"]["STATUS"]["CODE"];
            if($cvp == 200){
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
        return $message;
    }
}

return array(
    'value' => 'birsms',
    'label' => 'BirSMS',
    'fields' => array(
        'user','pass'
    )
);

