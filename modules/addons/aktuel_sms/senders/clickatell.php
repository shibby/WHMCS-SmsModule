<?php
class clickatell extends AktuelSms {

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

        $baseurl = "http://api.clickatell.com";

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

        $url = "$baseurl/http/auth?user=$params->user&password=$params->pass&api_id=$params->apiid&from=$params->senderid";
        $ret = file($url);
        $answer = is_array($ret)?json_encode($ret):$ret;
        $log[] = ("Answer from Clickatell: ".$answer);

        $sess = explode(":", $ret[0]);
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=$params->senderid";

            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                $log[] = ("Message Sent.");
            } else {
                $answer = is_array($ret)?json_encode($ret):$ret;
                $log[] = ("Message could not sent. Error: $answer");
                $error[] = ("Message could not sent. Error: $answer");
            }
        } else {
            $answer = is_array($ret)?json_encode($ret):$ret;
            $log[] = ("Message could not sent. Error: $answer");
            $error[] = ("Message could not sent. Error: $answer");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $send[1],
        );
    }

    function balance(){
		$params = $this->getParams();
        if($params->user && $params->pass && $params->apiid){
			$url = 	"http://api.clickatell.com/http/getbalance?api_id=$params->apiid&user=$params->user&password=$params->pass";
            $result = file_get_contents($url);
            $result = explode(" ",$result);
            $cvp = $result[1];
			if ($cvp == 001){
				return null;
			}else{
				return $result[1];
			}
        }else{
            return null;
        }
    }

    function report($msgid){
		$params = $this->getParams();
        if($params->user && $params->pass && $params->apiid && $msgid){		
			$url = "http://api.clickatell.com/http/querymsg?user=$params->user&password=$params->pass&api_id=$params->apiid&apimsgid=$msgid";
			$result = file_get_contents($url);	
			$result = explode(" ",$result);
			$cvp = $result[1];
            if ($cvp == 001){
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
        return $number;
    }
    //You can spesifically convert your message
    function utilmessage($message){
        return $message;
    }
}

return array(
    'value' => 'clickatell',
    'label' => 'ClickAtell',
    'fields' => array(
        'user','pass','apiid'
    )
);
