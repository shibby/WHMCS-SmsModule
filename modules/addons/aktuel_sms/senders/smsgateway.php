<?php
// Author: MichaelPhan
// Email: sonpython@gmail.com

class smsgateway extends AktuelSms {
    static $baseUrl = "https://smsgateway.me";

    function __construct($message,$gsmnumber){
        $this->message = $this->utilmessage($message);
        $this->gsmnumber = $this->utilgsmnumber($gsmnumber);
    }

    function createContact ($name,$number) {
        return $this->makeRequest('/api/v3/contacts/create','POST',['name' => $name, 'number' => $number]);
    }

    function getContacts ($page=1) {
       return $this->makeRequest('/api/v3/contacts','GET',['page' => $page]);
    }

    function getContact ($id) {
        return $this->makeRequest('/api/v3/contacts/view/'.$id,'GET');
    }


    function getDevices ($page=1)
    {
        return $this->makeRequest('/api/v3/devices','GET',['page' => $page]);
    }

    function getDevice ($id)
    {
        return $this->makeRequest('/api/v3/devices/view/'.$id,'GET');
    }

    function getMessages($page=1)
    {
        return $this->makeRequest('/api/v3/messages','GET',['page' => $page]);
    }

    function getSingleMessage($id)
    {
        return $this->makeRequest('/api/v3/messages/view/'.$id,'GET');
    }

    function sendMessageToNumber($to, $message, $device, $options=[]) {
        $query = array_merge(['number'=>$to, 'message'=>$message, 'device' => $device], $options);
        return $this->makeRequest('/api/v3/messages/send','POST',$query);
    }

    function sendMessageToManyNumbers ($to, $message, $device, $options=[]) {
        $query = array_merge(['number'=>$to, 'message'=>$message, 'device' => $device], $options);
        return $this->makeRequest('/api/v3/messages/send','POST', $query);
    }

    function sendMessageToContact ($to, $message, $device, $options=[]) {
        $query = array_merge(['contact'=>$to, 'message'=>$message, 'device' => $device], $options);
        return $this->makeRequest('/api/v3/messages/send','POST', $query);
    }

    function sendMessageToManyContacts ($to, $message, $device, $options=[]) {
        $query = array_merge(['contact'=>$to, 'message'=>$message, 'device' => $device], $options);
        return $this->makeRequest('/api/v3/messages/send','POST', $query);
    }

    function sendManyMessages ($data) {
        $query['data'] = $data;
        return $this->makeRequest('/api/v3/messages/send','POST', $query);
    }

    private function makeRequest ($url, $method, $fields=[]) {
        $params = $this->getParams();

        $fields['email'] = $params->email;
        $fields['password'] = $params->pass;

        $url = smsGateway::$baseUrl.$url;

        $fieldsString = http_build_query($fields);


        $ch = curl_init();

        if($method == 'POST')
        {
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch,CURLOPT_POSTFIELDS, $fieldsString);
        }
        else
        {
            $url .= '?'.$fieldsString;
        }

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_HEADER , false);  // we want headers
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec ($ch);

        $return['response'] = json_decode($result,true);

        if($return['response'] == false)
            $return['response'] = $result;

        $return['status'] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close ($ch);

        return $return;
    } 


    function send(){
        
        if($this->gsmnumber == "numbererror"){
            $log[] = ("Number format error.".$this->gsmnumber);
            $error[] = ("Number format error.".$this->gsmnumber);
            return null;
        }
        $params = $this->getParams();

        // check list of devices and get the first one:
        $getDevicesresult = $this->getDevices();
        $json_string = json_encode($getDevicesresult, JSON_PRETTY_PRINT);
        $deviceID = $getDevicesresult['response']['result']['data'][0]['id'];
        if (!$deviceID) {
            $log[] = "Can not get deviceID. error : ".$json_string;
            $error[] = "Can not get deviceID. error: ".$json_string;
        }

        // the mess number and content:
        $number = $this->gsmnumber;
        $message = $this->message;
        $options = [
            'expires_at' => strtotime('+20 minutes') // Cancel the message in 1 hour if the message is not yet sent
        ];

        // call API to send message:
        $result = $this->sendMessageToNumber($number, $message, $deviceID, $options);
        $json_string = json_encode($result, JSON_PRETTY_PRINT);
        
        $log[] = "Request send message: ".$message . 'to number: '.$number;

        $return = $result;
        $log[] = "smsGateway server response returned: ".$json_string;

      
        if ($result['response']['success']) {
            $this->addLog("Call API success.");
            $log[] = "Call API success.";
            $Status = $result['response']['result']['success'][0]['status'];
            $send_at = date('Y-m-d h:i:s',$result['response']['result']['success'][0]['send_at']);

            if ($result['response']['result']['success']['error']=="") {
                $messid = $result['response']['result']['success'][0]['id'];
                $this->addLog("Message id: " . $messid . " was sent at" . $send_at . "  Status: ".$Status);
                $log[] = "Message id: " . $messid . " sent at: " . $send_at . " Status: ".$Status;
            }elseif($result['response']['result']['fails']['errors']) {
                $error = json_encode($result['response']['result']['fails']['errors'], JSON_PRETTY_PRINT);
                $log[] = "Error when sending message. error : ".$error;
                $error[] = "An error occurred while sending messages. error: ".$error;
            }else{
                $log[] = "Unable to send message. error : ".$json_string;
                $error[] = "An error occurred while sending messages. error: ".$json_string;
            }
        }else{
            $log[] = "Unable to send message. error : ".$json_string;
            $error[] = "An error occurred while sending messages. error: ".$json_string;
        }
        return array(
            'log' => $log,
            'error' => $error,
            'msgid' => $messid,
        );
    }   

    function balance(){
        // check list of devices and get the first one:
        $getDevicesresult = $this->getDevices();
        $DeviceID = $getDevicesresult['response']['result']['data'][0]['id'];
        $Devicename = $getDevicesresult['response']['result']['data'][0]['name'];
        $Devicemake = $getDevicesresult['response']['result']['data'][0]['make'];
        $Devicemodel = $getDevicesresult['response']['result']['data'][0]['model'];
        $Devicenumber = $getDevicesresult['response']['result']['data'][0]['number'];
        $battery = $getDevicesresult['response']['result']['data'][0]['battery'];
        $Devicesignal = $getDevicesresult['response']['result']['data'][0]['signal'];
        $Devicewifi = $getDevicesresult['response']['result']['data'][0]['wifi'];
        $device_info = 'ID: '.$DeviceID.' | '.'Name: '.$Devicename. ' '.$Devicemake.' '.$Devicemodel.' | '.'Number: '.$Devicenumber.' | '.'Battery: '.$battery.' | '.'Devicesignal: '.$Devicesignal.' | '.'Devicewifi: '.$Devicewifi;

        if ($device_info) {
            return $device_info;
        }else {
            return 'Can not get Device\'s info';
        }
    }

    function report($msgid){
        $id = $msgid;
        $result = $this->getSingleMessage($id);

        if ($result['response']['success']) {
            $status = $result['response']['result']['status'];
            // $report = 'Status: ' . $status . '. Error' . $result['response']['result']['error'];
            return $status;
        }else {
            return 'Unknown';
        }
    }

    function utilgsmnumber($number){
        $params = $this->getParams();
        $countrycode = $params->countrycode;
        $Cnumber = $countrycode . substr($number, 1);
        return $Cnumber;
    }
    
    function utilmessage($message){
        // $params = $this->getParams();
        // $sign = $params->sign
        // $message = $message . ' ' . $sign
        return $message;
    }
}

return array(
    'value' => 'smsgateway',
    'label' => 'SMS Gateway',
    'fields' => array(
        'email','pass','countrycode'
    )
);
