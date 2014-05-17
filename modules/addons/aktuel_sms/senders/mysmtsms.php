<?php
class mysmtsms extends AktuelSms {

    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $th

       
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $result[2],
        );
    }

    function balance(){
        $params = $this->getP
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
