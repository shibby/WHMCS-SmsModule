<?php
class bytehand extends AktuelSms {

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


        $result = @file_get_contents('http://bytehand.com:3800/send?id='.$params->user.'&key='.$params->pass.'&to='.urlencode($this->gsmnumber).'&from='.urlencode($params->senderid).'&text='.urlencode($this->message));
        $result = json_decode($result);

        if($result->status == 0) {
            $log[] = ("Message sent.");
        } else {
            $log[] = ("Error.");
            $error[] = ("Check status, looks like problem with a connection or credentials.");
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $result->description,
        );
    }

    function balance(){
        $params = $this->getParams();
        if($params->user && $params->pass) {
            $result = @file_get_contents('http://bytehand.com:3800/balance?id='.$params->user.'&key='.$params->pass);
            $result = json_decode($result);

            if ($result->status == 0) {
                return $result->description;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    function report($msgid){
        $params = $this->getParams();
        if($params->user && $params->pass && $msgid){
            $result = @file_get_contents('http://bytehand.com:3800/status?id='.$params->user.'&key='.$params->pass.'&message='.$msgid);
            $result = json_decode($result);
            if ($result->status == 0) {
                if ($result->description == 'DELIVERED' || $result->description == 'ACCEPTED') {
                    return "success";
                } else {
                    return "error";
                }
            } else {
                null; // Problem with a connection, not with SMS.
            }
        } else {
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
    'value' => 'bytehand',
    'label' => 'ByteHand',
    'fields' => array(
        'user','pass'
    )
);
