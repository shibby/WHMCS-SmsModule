<?php
class msg91 extends AktuelSms {

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

		//Your authentication key (Go to https://control.msg91.com/apidoc/)
		$authKey = $params->authkey;
		
		//Base URL
		//Composed of initial common portion of URL of SMS Gateway Provider
		$baseurl = "https://control.msg91.com";
	
		//Sender ID, While using route 4 sender id should be 6 characters long.
		$senderId = trim($params->senderid);
		$senderId = substr($senderId, 0, 6);
		
		//Define route (SMS Delivery)
		//If route = 1 (Route 1 is Normal Route does not send sms to National Do Not Call registry(NDNC) Numbers and only before 9PM IST)
		//If route = 4 (Route 4 is Informative Route - 24 hours open)
		if(ctype_digit($params->route)){
			$smsRoute = $params->route;
		}else{
			$smsRoute = 1;			//Using Default route 1 if undefined in settings
		}
		
		//Define Message Type
			// Send Unicode Message
			// Yes = 1 / No = 0 (if No, Default is English)
			if(ctype_digit($params->unicode)){
				$unicodeSupport = $params->unicode;
			}else{
				$unicodeSupport = 0;			//Unicode support is disbaled if not defined in settings
			}
			
			// Send Flash Message (Dispay SMS directly on mobile screen)
			// Yes = 1 / No = 0 
			if(ctype_digit($params->flash)){
				$flashSupport = $params->flash;
			}else{
				$flashSupport = 0;			//Flash SMS support is disabled by default if undefined in settings
			}
			
			// Ignore NDNC
			// Yes = 1 / No = 0
			// ignoreNdnc=1 (if you want system to ignore all NDNC Numbers, useful while using route 4)
			if(ctype_digit($params->ignoreNdnc)){
				$ignoreNdnc = $params->ignoreNdnc;
			}else{
				$ignoreNdnc = 1;
			}

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

		// Validation of connection to SMS Gateway Server
        $url = "$baseurl/api/validate.php?authkey=$authKey&type=$smsRoute"; //verify connetion to gateway server
        $ret = file($url);
        $log[] = ("Response returned from the server: ".$ret);

        $sess = explode(",", $ret[0]);
        if ($sess[0] == "Valid") {
		
            $url = "$baseurl/api/sendhttp.php?authkey=$authKey&mobiles=$to&message=$text&sender=$senderId&route=$smsRoute&unicode=$unicodeSupport&flash=$flashSupport";
			echo $url;
            $ret = file($url);
            $send = array_map('trim',explode(":", $ret[0]));

            if ($send[0] != "CODE" && $send[0] != "Please") {
                $log[] = ("Message sent!");
            } else {
                $log[] = ("Message could not be sent. Error: $ret");
                $error[] = ("An error occurred while sending the message. Error: $ret");
            }
        } else {
            $log[] = ("Message could not be sent. Authentication Error: $ret[0]");
            $error[] = ("Authentication failed. $ret[0] ");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $send[0],
        );
    }

    function balance(){
		$params = $this->getParams();
        if($params->authkey && $params->route){
			$baseurl = "https://control.msg91.com";
			$url = 	"$baseurl/api/balance.php?authkey=$params->authkey&type=$params->route";
            $result = file_get_contents($url);
            $result = array_map('trim',explode(":",$result));
            $cvp = $result[1];
			if ($cvp == 001 || $cvp == 002){
				return null;
			}else{
				return $result[0];
			}
        }else{
            return null;
        }
    }

    function report($msgid){
		$params = $this->getParams();
        if($params->authkey && $msgid){
			$baseurl = "https://control.msg91.com";
			$url = "$baseurl/api/check_delivery.php?authkey=$params->authkey&requestid=$msgid";
			$result = file_get_contents($url);
			$result = array_map('trim',explode(":",$result));
			$cvp = $result[1];
            if ($cvp == 001 || $cvp == 002){
                return "error";
            }else{
                return "success";
            }
        }else{
            return null;
        }
    }

    //You can spesifically convert your gsm number. See netgsm for example
    function utilgsmnumber($number){
        if (strlen($number) == 10){
            $number = '91' . $number;
        }

        if (substr($number, 0, 2) != "91"){
            return "numbererror";
        }

        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'msg91',
    'label' => 'msg91.com (India)',
    'fields' => array(
        'authkey','route','flash','unicode','ignoreNdnc'
    )
);
