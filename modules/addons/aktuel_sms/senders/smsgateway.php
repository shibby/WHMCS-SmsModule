<?php
include('../lib/smsGateway.php');

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

        // add a new SMSGateway object
        $smsGateway = new SmsGateway($params->email, $params->pass);

        // check list of devices and get the first one:
        $page = 1;
        $getDevicesresult = $smsGateway->getDevices($page);
        $deviceID = $getDevicesresult->result->data[0]->id;

        // the mess number and content:
        $number = $this->gsmnumber;
        $message = $this->message;
        $options = [
            'expires_at' => strtotime('+20 minutes') // Cancel the message in 1 hour if the message is not yet sent
        ];

        // call API to send message:
        $result = $smsGateway->sendMessageToNumber($number, $message, $deviceID, $options);
		
        $log[] = "Request send message: ".$message . 'to number: '.$number;

        $return = $result;
        $log[] = "smsGateway server response returned: ".$result;

      
        if ($result->success) {
            $this->addLog("Call API success.");
            $log[] = "Call API success.";
            $Status = $result->result->success->status;
            $send_at = date('Y-m-d h:i:s',$result->result->success->send_at);

            if ($result->result->success->id) {
                $messid = $result->result->success->id;
                $this->addLog("Message id: " . $messid . " sent at" . $send_at . "Status: ".$Status);
                $log[] = "Message id: " . $messid . " sent at" . $send_at . "Status: ".$Status;
            }elseif($result->result->fails->errors) {
                $error = $result->result->fails->errors->text;
                $log[] = "Error when sending message. error : $return";
                $error[] = "An error occurred while sending messages. error: $return";
            }else{
            $log[] = "Unable to send message. error : $return";
            $error[] = "An error occurred while sending messages. error: $return";
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
        $params = $this->getParams();

        // add a new SMSGateway object
        $smsGateway = new SmsGateway($params->email, $params->pass);

        // check list of devices and get the first one:
        $page = 1;
        $getDevicesresult = $smsGateway->getDevices($page);
        $battery = $getDevicesresult->result->data[0]->battery;
        if ($battery) {
            return $battery;
        }else {
            return null;
        }
    }

    function report($msgid){
        $params = $this->getParams();

        // add a new SMSGateway object
        $smsGateway = new SmsGateway($params->email, $params->pass);
        $id = $msgid;

        $result = $smsGateway->getMessage($id);

        if ($result->success) {
            $report = 'Status: ' . $result->result->status . '. Error' . $result->result->error;
            return $report;
        }else {
            return 'SMS sending fails. Unknown Error $result';
        }
    }

    function utilgsmnumber($number){
        $params = $this->getParams();
        $countrycode = $params->user
        $number = $countrycode . substr($number, 1);
        return $number;
    }
	
    function utilmessage($message){
        $params = $this->getParams();
        $sign = $params->sign
        $message = $message . ' ' . $sign
        return $message;
    }
}

return array(
    'value' => 'smsgateway',
    'label' => 'SMS Gateway',
    'fields' => array(
        'email','pass','countrycode','sign'
    )
);
