<?php

/**
 * @author Guven Atbakan <guven@atbakan.com>
 */
class sendsmsro extends AktuelSms implements SmsSenderInterface
{
    public function __construct($message, $gsmnumber)
    {
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
    }

    public function send()
    {
        if ($this->gsmnumber == "numbererror") {
            $log[] = ("Number format error." . $this->gsmnumber);
            $error[] = ("Number format error." . $this->gsmnumber);
            return null;
        }
        $params = $this->getParams();

        $url = "http://api.sendsms.ro/json?action=message_send&username={$params->user}&password={$params->pass}&to={$this->gsmnumber}&text=" . urlencode($this->message) . "&from=$params->senderid";
        $log[] = "Request url: " . $url;
        $result = file_get_contents($url);

        $log[] = "Sunucudan dönen cevap: " . $result;

        $result = json_decode($result, true);
        if (isset($result['status']) && $result['status'] == 1) {
            $this->addLog("Message sent.");
            $log[] = "Message sent";
            $msgid = $result['details'];
            $log[] = "Message id: " . $msgid;
        } else {
            $log[] = "ERROR: " . $result['message'];
            $error[] = "ERROR: " . $result['message'];
        }

        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $msgid,
        );
    }

    public function balance()
    {
        return null;
    }

    public function report($msgid)
    {
        return null;
    }

    //You can spesifically convert your gsm number. See netgsm for example
    public function utilgsmnumber($number)
    {
        return $number;
    }

    //You can spesifically convert your message
    public function utilmessage($message)
    {
        return $message;
    }
}

return array(
    'value' => 'sendsmsro',
    'label' => 'SendSms.ro',
    'fields' => array(
        'user', 'pass'
    )
);