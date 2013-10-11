<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

class AktuelSms{
    var $sender;

    var $params;
    var $gsmnumber;
    var $message;
    var $userid;
    var $errors = array();
    var $logs = array();

    function getParams(){
        $params = json_decode($this->params);
        $this->addLog("SenderId: ".$params->senderid);
        return $params;
    }

    function send(){
        $this->gsmnumber = $this->util_gsmnumber($this->gsmnumber,$this->sender);
        $this->message = $this->util_convert($this->message);

        $sender_function = "Send" . $this->sender;

        $this->addLog("TO: ".$this->gsmnumber);
        $this->addLog("Message: ".$this->message);
        $this->addLog("Sender: ".$sender_function);

        $this->$sender_function();
    }

    function getSenders(){
        return array(
            array(
                'key' => 'ClickAtell',
                'value' => 'ClickAtell'
            ),
            array(
                'key' => 'NetGsm',
                'value' => 'NetGsm'
            ),
            array(
                'key' => 'UcuzSmsAl',
                'value' => 'UcuzSmsAl'
            ),
            array(
                'key' => 'Mutlucell',
                'value' => 'Mutlucell'
            ),
            array(
                'key' => 'IletiMerkezi',
                'value' => 'İleti Merkezi'
            ),
            array(
                'key' => 'Hemenposta',
                'value' => 'Hemenposta'
            ),
        );
    }

    function SendMutlucell(){
        $params = $this->getParams();
		// XML - formatında data
		$xml_data ='<?xml version="1.0" encoding="UTF-8"?>'.
		'<smspack ka="'.$params->user.'" pwd="'.$params->pass.'" org="'.$params->senderid.'" >'.
			'<mesaj>'.
				'<metin>'.$this->message.'</metin>'.
				'<nums>'.$this->gsmnumber.'</nums>'.
			'</mesaj>'.
		'</smspack>';	
		$URL = "https://smsgw.mutlucell.com/smsgw-ws/sndblkex";
        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_MUTE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, "$xml_data");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);        
		curl_close($ch);
        $return = $result;
        $this->addLog("Geri Dönüş Kodu: ".$result);
		$h0 =	20;
		$h1=	21;
		$h2=	22;
		$h3=	23;
		$h4=	24;
		$h5=	25;
		$h6=	30;
	if($return == $h0):    
		$this->addLog("Post edilen xml eksik veya hatalı.Hata Kodu: $return");
		$this->addError("Post edilen xml eksik veya hatalı.Hata Kodu: $return");
	elseif($return == $h1):    
		$this->addLog("Kullanılan originatöre sahip değilsiniz.Hata Kodu: $return");
		$this->addError("Kullanılan originatöre sahip değilsiniz.Hata Kodu: $return");
	elseif($return == $h2):    
		$this->addLog("Kontörünüz yetersiz.Hata Kodu: $return");
		$this->addError("Kontörünüz yetersiz.Hata Kodu: $return");
	elseif($return == $h3):    
		$this->addLog("Kullanıcı adı ya da parolanız hatalı. Hata Kodu: $return");
		$this->addError("Kullanıcı adı ya da parolanız hatalı. Hata Kodu: $return");
	elseif($return == $h4):    
		$this->addLog("Şu anda size ait başka bir işlem aktif.Hata Kodu: $return");
		$this->addError("Şu anda size ait başka bir işlem aktif.Hata Kodu: $return");
	elseif($return == $h5):  
		$this->addLog("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.Hata Kodu: $return");
		$this->addError("Bu hatayı alırsanız, işlemi 1-2 dk sonra tekrar deneyin.Hata Kodu: $return");
	elseif($return == $h6):    
		$this->addLog("Hesap Aktivasyonu sağlanmamış.Hata Kodu: $return");
		$this->addError("Hesap Aktivasyonu sağlanmamış.Hata Kodu: $return");
	else:
		$this->addLog("Mesaj Başarıyla Gönderildi.");
		$this->saveToDb($result);
	endif;
    }
    
    function SendHemenposta(){
        $params = $this->getParams();

		$postUrl = "http://sms.modexi.com/service/sendxml";
		// XML - formatında data
		$xmlString="<SMS><authentification><username>$params->user</username><password>$params->pass</password></authentification><message><sender>$params->senderid</sender></message><recipients><text>$this->message</text><gsm>$this->gsmnumber</gsm></recipients></SMS>";

		$fields = $xmlString;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $postUrl);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$result = curl_exec($ch);
		curl_close($ch);

        $return = $result;
        $this->addLog("Result from server: ".$result);

        if(preg_match('/<status>(.*?)<\/status>(.*?)<DESC>(.*?)<\/DESC>(.*?)<package>(.*?)<\/package>/si', $result, $result_matches)) {
            $status_code = $result_matches[1];
            $status_message = $result_matches[3];
            $order_id = $result_matches[5];

            if($status_code > 0) {
                $this->addLog("Message sent.");
                $this->saveToDb($order_id);
            } else {
                $this->addLog("Message sent failed. Error: $status_message");
                $this->addError("Send message failed. Error: $status_code");
            }
        } else {
            $this->addLog("Message sent failed. Error: $return");
            $this->addError("Send message failed. Error: $return");
        }
    }

    function SendClickAtell(){
        $params = $this->getParams();

        $baseurl = "http://api.clickatell.com";

        $text = urlencode($this->message);
        $to = $this->gsmnumber;

        $url = "$baseurl/http/auth?user=$params->user&password=$params->pass&api_id=$params->apiid&from=$params->senderid";
        $ret = file($url);
        $this->addLog("Result from server: ".$ret);

        $sess = explode(":", $ret[0]);
        if ($sess[0] == "OK") {

            $sess_id = trim($sess[1]); // remove any whitespace
            $url = "$baseurl/http/sendmsg?session_id=$sess_id&to=$to&text=$text&from=$params->senderid";

            $ret = file($url);
            $send = explode(":", $ret[0]);

            if ($send[0] == "ID") {
                $this->addLog("Message sent.");
                $this->saveToDb($send[1]);
            } else {
                $this->addLog("Message sent failed. Error: $ret");
                $this->addError("Send message failed. Error: $ret");
            }
        } else {
            $this->addLog("Message sent failed. Authentication Error: $ret[0]");
            $this->addError("Authentication failed. $ret[0] ");
        }
    }

    function SendIletiMerkezi() {
        $params = $this->getParams();

        $url = "http://api.iletimerkezi.com/v1/send-sms/get/?username=$params->user&password=$params->pass&receipents=$this->gsmnumber&text=".urlencode($this->message)."&sender=".urlencode($params->senderid);

        $result = file_get_contents($url);
        $return = $result;
        $this->addLog("Result from server: ".$result);

        if(preg_match('/<status>(.*?)<code>(.*?)<\/code>(.*?)<message>(.*?)<\/message>(.*?)<\/status>(.*?)<order>(.*?)<id>(.*?)<\/id>(.*?)<\/order>/si', $result, $result_matches)) {
            $status_code = $result_matches[2];
            $status_message = $result_matches[4];
            $order_id = $result_matches[8];

            if($status_code == '200') {
                $this->addLog("Message sent.");
                $this->saveToDb($order_id);
            } else {
                $this->addLog("Message sent failed. Error: $status_message");
                $this->addError("Send message failed. Error: $status_message");
            }
        } else {
            $this->addLog("Message sent failed. Error: $return");
            $this->addError("Send message failed. Error: $return");
        }
    }

    function SendNetGsm(){
        $params = $this->getParams();

        $url = "http://api.netgsm.com.tr/bulkhttppost.asp?usercode=$params->user&password=$params->pass&gsmno=$this->gsmnumber&message=".urlencode($this->message)."&msgheader=$params->senderid";
        $result = file_get_contents($url);
//        $result = "00 01";
        $return = $result;
        $this->addLog("Result from server: ".$result);

        $result = explode(" ", $result);
        if ($result[0] == "00" || $result[0] == "01" || $result[0] == "02") {
            $this->addLog("Message sent.");
            $this->saveToDb($result[1]);
        }else{
            $this->addLog("Message sent failed. Error: $return");
            $this->addError("Send message failed. Error: $return");
        }

    }

    function SendUcuzSmsAl(){
        $params = json_decode($this->params);

        $url = "http://www.ucuzsmsal.com/api/index.php?act=sendsms&user=".$params->user."&pass=".$params->pass."&orgin=".$params->senderid."&message=".urlencode($this->message)."&numbers=$this->gsmnumber";

        $result = file_get_contents($url);
        $return = $result;
        $this->addLog("Result from server: ".$result);

        $result = explode("|",$result);
        if($result[0]=="OK"){
            $this->addLog("Message sent.");
            $this->saveToDb($result[1]);
        }else{
            $this->addLog("Message sent failed. Error: $return");
            $this->addError("Send message failed. Error: $return");
        }

    }

    function saveToDb($msgid){
        $now = date("Y-m-d H:i:s");
        $table = "mod_aktuelsms_messages";
        $values = array("sender" => $this->sender, "to" => $this->gsmnumber, "text" => $this->message, "msgid" => $msgid, "status" => '', "user" => $this->userid, "datetime" => $now);
        insert_query($table, $values);

        $this->addLog("Message saved to db");
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
    function util_gsmnumber($number,$sender){
        /* In this function i have removed special chars and
         * controlled number if it is real?
         * All numbers in Turkey starts with 0905 */
        $replacefrom = array('-', '(',')', '.', '+', ' ');
        $number = str_replace($replacefrom, '', $number);
        if (strlen($number) < 10) {
            $this->addLog("Number format is not correct: ".$number);
            $this->addError("Number format is not correct: ".$number);
            return null;
        }

        if($sender == "ClickAtell"){

        }elseif($sender == "UcuzSmsAl"){

            if (strlen($number) == 10) {

            } elseif (strlen($number) == 11) {
                $number = substr($number,1,strlen($number));
            } elseif (strlen($number) == 12) {
                $number = substr($number,2,strlen($number));
            }

            if (substr($number, 0, 1) != "5") {
                $this->addLog("Number format is not correct: ".$number);
                $this->addError("Number format is not correct: ".$number);
                return null;
            }
        }elseif($sender == "NetGsm"){
            if (strlen($number) == 10) {
                $number = '90' . $number;
            } elseif (strlen($number) == 11) {
                $number = '9' . $number;
            }

            if (substr($number, 0, 3) != "905") {
                $this->addLog("Number format is not correct: ".$number);
                $this->addError("Number format is not correct: ".$number);
                return null;
            }
        }

        return $number;
    }

    function addError($error){
        $this->errors[] = $error;
    }

    function addLog($log){
        $this->logs[] = $log;
    }

    function getHooks(){

        $hooks[] = array(
            'hook' => 'ClientChangePassword',
            'function' => 'ClientChangePassword',
            'type' => 'client',
            'extra' => '',
			'variables' => '{firstname},{lastname}',
			'defaultmessage' => 'Sayin {firstname} {lastname}, sifreniz degistirildi. Eger bu islemi siz yapmadiysaniz lutfen bizimle iletisime gecin.',
        );
        $hooks[] = array(
            'hook' => 'TicketAdminReply',
            'function' => 'TicketAdminReply',
            'type' => 'client',
            'extra' => '',
            'variables' => '{firstname},{lastname},{ticketsubject}',
			'defaultmessage' => 'Sayin {firstname} {lastname}, ({ticketsubject}) konu baslikli destek talebiniz yanitlandi.',
        );
        $hooks[] = array(
            'hook' => 'ClientAdd',
            'function' => 'ClientAdd',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, AktuelHost a kayit oldugunuz icin tesekkur ederiz. Email: {email} Sifre: {password}',
			'variables' => '{firstname},{lastname},{email},{password}'
        );
        #Domain
        $hooks[] = array(
            'hook' => 'AfterRegistrarRegistration',
            'function' => 'AfterRegistrarRegistration',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, alan adiniz basariyla kayit edildi. ({domain})',
			'variables' => '{firstname},{lastname},{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRenewal',
            'function' => 'AfterRegistrarRenewal',
            'type' => 'client',
            'extra' => '',
            'defaultmessage' => 'Sayin {firstname} {lastname}, alan adiniz basariyla yenilendi. ({domain})',
            'variables' => '{firstname},{lastname},{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRegistrationFailed',
            'function' => 'AfterRegistrarRegistrationFailed',
            'type' => 'client',
            'extra' => '',
            'defaultmessage' => 'Sayin {firstname} {lastname}, alan adiniz kayit edilemedi. En kisa surede lutfen bizimle iletisime gecin ({domain})',
            'variables' => '{firstname},{lastname},{domain}'
        );
        #Product
        $hooks[] = array(
            'hook' => 'AfterModuleCreate',
            'function' => 'AfterModuleCreate_Hosting',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {domain} icin hosting hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}',
			'variables' => '{firstname}, {lastname}, {domain}, {username}, {password}'
        );
        $hooks[] = array(
            'hook' => 'AfterModuleCreate',
            'function' => 'AfterModuleCreate_Reseller',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {domain} icin hosting hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}',
			'variables' => '{firstname}, {lastname}, {domain}, {username}, {password}'
        );
        $hooks[] = array(
            'hook' => 'AfterModuleSuspend',
            'function' => 'AfterModuleSuspend',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, hizmetiniz duraklatildi. ({domain})',
			'variables' => '{firstname},{lastname},{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterModuleUnsuspend',
            'function' => 'AfterModuleUnsuspend',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, hizmetiniz tekrar aktiflestirildi. ({domain})',
			'variables' => '{firstname},{lastname},{domain}'
        );
        #Order
        $hooks[] = array(
            'hook' => 'AcceptOrder',
            'function' => 'AcceptOrder_SMS',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {orderid} numarali siparisiniz onaylanmistir. ',
			'variables' => '{firstname},{lastname},{orderid}'
        );
        #Invoice
        $hooks[] = array(
            'hook' => 'InvoicePaymentReminder',
            'function' => 'InvoicePaymentReminder_Reminder',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com',
			'variables' => '{firstname}, {lastname}, {duedate}'
        );
        $hooks[] = array(
            'hook' => 'InvoicePaymentReminder',
            'function' => 'InvoicePaymentReminder_Firstoverdue',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com',
			'variables' => '{firstname}, {lastname}, {duedate}'
        );
        $hooks[] = array(
            'hook' => 'InvoiceCreated',
            'function' => 'InvoiceCreated',
            'type' => 'client',
            'extra' => '',
			'defaultmessage' => 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir fatura olusturulmustur. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com',
			'variables' => '{firstname}, {lastname}, {duedate}'
        );
        #Cron
        $hooks[] = array(
            'hook' => 'DailyCronJob',
            'function' => 'DomainRenewalNotice',
            'type' => 'client',
            'extra' => '15',
            'defaultmessage' => 'Sayin {firstname} {lastname}, {domain} alanadiniz {expirydate}({x} gun sonra) tarihinde sona erecektir. Yenilemek icin sitemizi ziyaret edin. www.aktuelhost.com',
			'variables' => '{firstname}, {lastname}, {domain},{expirydate},{x}'
        );

        #Admin
        $hooks[] = array(
            'hook' => 'ClientAdd',
            'function' => 'ClientAdd_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Sitenize yeni musteri kayit oldu.',
			'variables' => ''
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRegistration',
            'function' => 'AfterRegistrarRegistration_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Yeni domain kayit edildi. {domain}',
            'variables' => '{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRenewal',
            'function' => 'AfterRegistrarRenewal_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Domain yenilendi. {domain}',
            'variables' => '{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRegistrationFailed',
            'function' => 'AfterRegistrarRegistrationFailed_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Domain kayit edilirken hata olustu. {domain}',
            'variables' => '{domain}'
        );
        $hooks[] = array(
            'hook' => 'AfterRegistrarRenewalFailed',
            'function' => 'AfterRegistrarRenewalFailed_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Domain yenilenirken hata olustu. {domain}',
            'variables' => '{domain}'
        );
        $hooks[] = array(
            'hook' => 'TicketOpen',
            'function' => 'TicketOpen_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Yeni bir ticket acildi. ({subject})',
            'variables' => '{subject}'
        );
        $hooks[] = array(
            'hook' => 'TicketUserReply',
            'function' => 'TicketUserReply_admin',
            'type' => 'admin',
            'extra' => '',
            'defaultmessage' => 'Bir ticket musteri tarafindan guncellendi. ({subject})',
            'variables' => '{subject}'
        );

        return $hooks;
    }

    function checkHooks($hooks = null){
        if($hooks == null){
            $hooks = $this->getHooks();
        }

        $i=0;
        foreach($hooks as $hook){
            $sql = "SELECT `id` FROM `mod_aktuelsms_templates` WHERE `name` = '".$hook['function']."' AND `type` = '".$hook['type']."' LIMIT 1";
            $result = mysql_query($sql);
            $num_rows = mysql_num_rows($result);
            if($num_rows == 0){
                $values = array(
                    "name" => $hook['function'],
                    "type" => $hook['type'],
                    "template" => $hook['defaultmessage'],
                    "variables" => $hook['variables'],
                    "extra" => $hook['extra'],
                    "active" => 1
                );
                insert_query("mod_aktuelsms_templates", $values);
                $i++;
            }
        }
        return $i;
    }

    function getTemplateDetails($template = null){
        $where = array("name" => array("sqltype" => "LIKE", "value" => $template));
        $result = select_query("mod_aktuelsms_templates", "*", $where);
        $data = mysql_fetch_assoc($result);

        return $data;
    }

    function getSettings(){
        return mysql_fetch_assoc(mysql_query("SELECT * FROM `mod_aktuelsms_settings` LIMIT 1"));
    }

}
