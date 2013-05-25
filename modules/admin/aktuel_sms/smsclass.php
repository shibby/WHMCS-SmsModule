<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

class SendGsm{
    var $sender;

    var $params;
    var $gsmnumber;
    var $message;
    var $userid;

    function send(){
        $this->gsmnumber = $this->util_gsmnumber($this->gsmnumber);
        $this->message = $this->util_convert($this->message);

        $sender_function = "Send" . $this->sender;
        $this->$sender_function();
    }

    function SendClickAtell(){

        $params = json_decode($this->params);
        $senderid = $params->senderid;
        $user = $params->user;
        $password = $params->pass;
        $api_id = $params->apiid;
        $baseurl = "http://api.clickatell.com";

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

        $url = "$baseurl/http/auth?user=$user&password=$password&api_id=$api_id&from=$senderid";
        $ret = file($url);

        $sess = explode(":", $ret[0]);
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=$senderid";

// do sendmsg call
            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                $this->saveToDb($send[1]);
//$send[1];
            } else {
//echo "send message failed";
            }
        } else {
//echo "Authentication failure: ". $ret[0];
        }

    }

    function SendNetGsm(){
        $params = json_decode($this->params);
        $senderid = $params->senderid;
        $user = $params->user;
        $password = $params->pass;


        $xml = '<?xml version="1.0" encoding="iso-8859-9"?>
        <mainbody>
            <header>
                <company>NETGSM</company>
                <usercode>' . $user . '</usercode>
                <password>' . $password . '</password>
                <startdate></startdate>
                <stopdate></stopdate>
                <type>1:n</type>
                <msgheader>' . $senderid . '</msgheader>
            </header>
            <body>
            <msg><![CDATA[' . $this->message . ']]></msg>
            <no>' . $this->gsmnumber . '</no>
            </body>
        </mainbody>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.netgsm.com.tr/xmlbulkhttppost.asp");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);
        //$result = "01 123456";
        $result = explode(" ", $result);
        if ($result[0] == "00" || $result[0] == "01" || $result[0] == "02") {
            $this->saveToDb($result[1]);
        }

    }

    function SendUcuzSmsAl(){
        $params = json_decode($this->params);
        $senderid = $params->senderid;
        $user = $params->user;
        $password = $params->pass;

        $xml = '
        <SMS>
        <oturum>
            <kullanici>' . $user . '</kullanici>
            <sifre>' . $password . '</sifre>
        </oturum>
        <mesaj>
            <baslik>' . $senderid . '</baslik>
            <metin>' . $this->message . '</metin>
            <alicilar>' . $this->gsmnumber . '</alicilar>
        </mesaj>
        </SMS>';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://www.ucuzsmsal.com//api/xml_api.php");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, Array("Content-Type: text/xml"));
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        $result = curl_exec($ch);

        $this->saveToDb($result[1]);

    }

    function saveToDb($msgid){
        $now = date("Y-m-d H:i:s");
        $table = "mod_aktuelsms_messages";
        $values = array("sender" => $this->sender, "to" => $this->gsmnumber, "text" => $this->message, "msgid" => $msgid, "status" => '', "user" => $this->userid, "datetime" => $now);
        insert_query($table, $values);
    }

    /* Here you can change anything from your message string */
    function util_convert($message){
        /* In this function i have changed Turkish characters to
        English chars.
        */
        $changefrom = array('ı', 'İ', 'ü', 'Ü', 'ö', 'Ö', 'ğ', 'Ğ', 'ç', 'Ç');
        $changeto = array('i', 'I', 'u', 'U', 'o', 'O', 'g', 'G', 'c', 'C');
        return str_replace($changefrom, $changeto, $message);
    }

    /* Here you can specify gsm numbers to your country */
    function util_gsmnumber($number){
        /* In this function i have removed special chars and
         * controlled number if it is real?
         * All numbers in Turkey starts with 0905 */
        $replacefrom = array('-', '(',')', '.', '+', ' ');
        $number = str_replace($replacefrom, '', $number);
        if (strlen($number) < 10) {
            return null;
        }

        if (strlen($number) == 10) {
            $number = '090' . $number;
        } elseif (strlen($number) == 11) {
            $number = '09' . $number;
        } elseif (strlen($number) == 12) {
            $number = '0' . $number;
        }

        if (substr($number, 0, 4) != "0905") {
            return null;
        }

        return $number;
    }

}
