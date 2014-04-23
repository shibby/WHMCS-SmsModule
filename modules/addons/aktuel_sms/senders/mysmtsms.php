<?php
class mysmtsms extends AktuelSms {

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

        $result = @file_get_contents('http://www.mysmtsms.com/components/com_spc/smsapi.php?username='.$params->user.'&password='.$params->pass.'&sender='.urlencode($params->senderid).'&recipient='.urlencode($this->gsmnumber).'&message='.urlencode($this->message).'&');
        $result = explode(" ",$result);

        if($result[0] == "OK") {
            $log[] = ("Message sent.");
        } elseif($result[0] == "2904") {
            $log[] = ("SMS Sending Failed.");
            $error[] = ("SMS Sending Failed.");
        } elseif($result[0] == "2905") {
            $log[] = ("Invalid username/password combination.");
            $error[] = ("Invalid username/password combination.");
        } elseif($result[0] == "2906") {
            $log[] = ("Credit exhausted.");
            $error[] = ("Credit exhausted.");
        } elseif($result[0] == "2907") {
            $log[] = ("Gateway unavailable.");
            $error[] = ("Gateway unavailable.");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $result[2],
        );
    }

    function balance(){
        $params = $this->getParams();
        if($params->user && $params->pass) {
            $result = @file_get_contents('http://mysmtsms.com/components/com_spc/smsapi.php?username='.$params->user.'&password='.$params->pass.'&balance=true&');

            if ($result) {
                return $result;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function report($msgid){
        return "success";
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
    'value' => 'mysmtsms',
    'label' => 'MySmtSms',
    'fields' => array(
        'user','pass'
    )
);
