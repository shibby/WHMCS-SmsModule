<?php

class onetouch extends AktuelSms {
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

        $url = "http://api.ottbd.org/api/v3/sendsms/plain?user=$params->user&password=$params->pass&GSM=$this->gsmnumber&sender=$params->senderid&messagetext=".urlencode($this->message)."";
		
        $log[] = "Request url: ".$url;
        $result = simplexml_load_file($url);

        $return = $result;
        $log[] = "server response returned: ".$result;

      
        if ($result->result[0]->status == "0") {
            $this->addLog("Message sent.");
            $log[] = "Message sent";
            $msgid = $result->result[0]->messageid;
            $log[] = "Message id: ".$msgid;
			
        }elseif($result->result[0]->status == "-1"){
            $log[] = "Error in processing the request ";
            $error[] = "Error in processing the request "; 
			
        }elseif($result->result[0]->status == "-2"){
            $log[] = "Not enough credit on a specific account ";
            $error[] = "Not enough credit on a specific account ";
			
        }elseif($result->result[0]->status == "-3"){
            $log[] = "Targeted network is not covered on this account ";
            $error[] = "Targeted network is not covered on this account ";
			
        }elseif($result->result[0]->status == "-5"){
            $log[] = "Invalid username or password ";
            $error[] = "Invalid username or password ";
			
        }elseif($result->result[0]->status == "-6"){
            $log[] = "Destination address is missing ";
            $error[] = "Destination address is missing ";
			
        }elseif($result->result[0]->status == "-10"){
            $log[] = "Username is missing ";
            $error[] = "Username is missing ";
			
        }elseif($result->result[0]->status == "-11"){
            $log[] = "Password is missing ";
            $error[] = "Password is missing ";
			
        }elseif($result->result[0]->status == "-13"){
            $log[] = "Number is not recognized by OneTouch platform ";
            $error[] = "Number is not recognized by OneTouch platform  ";
			
        }elseif($result->result[0]->status == "-33"){
            $log[] = "Duplicated MessageID ";
            $error[] = "Duplicated MessageID ";
			
        }elseif($result->result[0]->status == "-34"){
            $log[] = "Sender name not allowed ";
            $error[] = "Sender name not allowed ";
			
        }else{
            $log[] = "Unable to send message. error : $return";
            $error[] = "An error occurred while sending messages. error: $return";
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


    function utilgsmnumber($number){
        return $number;
    }
	
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'onetouch',
    'label' => 'OneTouchSMS',
    'fields' => array(
        'user','pass'
    )
);
