<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * AktuelHost - http://www.aktuelhost.com
 *
 * https://github.com/AktuelSistem/WHMCS-SmsModule
 *
 * Developed at Aktuel Sistem ve Bilgi Teknolojileri (www.aktuelsistem.com)
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */

if (!defined("WHMCS")) {
    exit("This file cannot be accessed directly");
}

function aktuel_sms_activate() {

    $sql = "CREATE TABLE IF NOT EXISTS `mod_aktuelsms_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`sender` varchar(40) NOT NULL,`to` varchar(15) NOT NULL,`text` text NOT NULL,`msgid` varchar(50) NOT NULL,`status` varchar(10) NOT NULL,`user` int(11) NOT NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    mysql_query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `mod_aktuelsms_settings` ( `id` int(11) NOT NULL AUTO_INCREMENT,`api` varchar(40) NOT NULL,`apiparams` varchar(100) NOT NULL,`wantsmsfield` int(11) NOT NULL,`gsmnumberfield` int(11) NOT NULL,`path` varchar(100) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;";
    mysql_query($sql);

    $sql = "INSERT INTO `mod_aktuelsms_settings` (`api`, `apiparams`, `wantsmsfield`, `gsmnumberfield`, `path`) VALUES ('', '', 0, 0, '');";
    mysql_query($sql);

    $sql = "CREATE TABLE IF NOT EXISTS `mod_aktuelsms_templates` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) NOT NULL,`type` enum('client','admin') NOT NULL,`admingsm` varchar(255) NOT NULL,`template` varchar(240) NOT NULL,`variables` varchar(500) NOT NULL,`active` tinyint(1) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13;";
    mysql_query($sql);

    $sql = "INSERT INTO `mod_aktuelsms_templates` (`id`, `name`, `type`, `admingsm`, `template`, `variables`, `active`) VALUES
            (1, 'TicketAdminReply', 'client', '', 'Sayin {firstname} {lastname}, ({ticketsubject}) konu baslikli destek talebiniz yanitlandi.', '{firstname},{lastname},{ticketid},{ticketsubject}', 1, ''),
            (2, 'ClientChangePassword', 'client', '', 'Sayin {firstname} {lastname}, sifreniz degistirildi. Eger bu islemi siz yapmadiysaniz lutfen bizimle iletisime gecin.', '{firstname},{lastname}', 1, ''),
            (3, 'ClientAdd', 'client', '', 'Sayin {firstname} {lastname}, AktuelHost a kayit oldugunuz icin tesekkur ederiz. Email: {email} Sifre: {password}', '{firstname},{lastname},{email},{password}', 1, ''),
            (4, 'AfterRegistrarRegistration', 'client', '', 'Sayin {firstname} {lastname}, alan adiniz basariyla kayit edildi. ({domain})', '{firstname},{lastname},{domain}', 1, ''),
            (5, 'AfterRegistrarRenewal', 'client', '', 'Sayin {firstname} {lastname}, alan adiniz basariyla yenilendi. ({domain})', '{firstname},{lastname},{domain}', 1, ''),
            (6, 'AfterModuleCreate_SharedAccount', 'client', '', 'Sayin {firstname} {lastname}, {domain} icin hosting hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, ''),
            (7, 'AfterModuleCreate_ResellerAccount', 'client', '', 'Sayin {firstname} {lastname}, {domain} icin reseller hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, ''),
            (8, 'AcceptOrder', 'client', '', 'Sayin {firstname} {lastname}, {orderid} numarali siparisiniz onaylanmistir. ', '{firstname},{lastname},{orderid}', 0, ''),
            (9, 'DomainRenewalNotice', 'client', '', 'Sayin {firstname} {lastname}, {domain} alanadiniz {expirydate}({x} gun sonra) tarihinde sona erecektir. Yenilemek icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {domain},{x}', 1, '15'),
            (10, 'InvoicePaymentReminder', 'client', '', 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {duedate}', 1, ''),
            (11, 'InvoicePaymentReminder_FirstOverdue', 'client', '', 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {duedate}', 1, '');";

    mysql_query($sql);
}

$tab = $_GET['tab'];

echo '
<div id="clienttabs">
    <ul>
	    <li class="' . (($tab == "settings")?"tabselected":"tab") . '"><a href="addonmodules.php?module=aktuel_sms&tab=settings">Settings</a></li>
		<li class="' . ((@$_GET['type'] == "client")?"tabselected":"tab") . '"><a href="addonmodules.php?module=aktuel_sms&tab=templates&type=client">Client SMS Templates</a></li>
		<li class="' . ((@$_GET['type'] == "admin")?"tabselected":"tab") . '"><a href="addonmodules.php?module=aktuel_sms&tab=templates&type=admin">Admin SMS Templates</a></li>
		<li class="' . (($tab == "sendbulk")?"tabselected":"tab") . '"><a href="addonmodules.php?module=aktuel_sms&tab=sendbulk">Send SMS</a></li>
		<li class="' . (($tab == "messages")?"tabselected":"tab") . '"><a href="addonmodules.php?module=aktuel_sms&amp;tab=messages">Messages</a></li>
		</ul>
</div>
';
if (!isset($tab) || $tab == "settings")
{

    /* UPDATE SETTINGS */
    if ($_POST['params']) {
        $update = array(
            "api" => $_POST['api'],
            "apiparams" => json_encode($_POST['params']),
            'wantsmsfield' => $_POST['wantsmsfield'],
            'gsmnumberfield' => $_POST['gsmnumberfield'],
            'path' => $_POST['path']
        );
        update_query("mod_aktuelsms_settings", $update, "");

    }
    /* UPDATE SETTINGS */

    $result = mysql_query("SELECT * FROM mod_aktuelsms_settings LIMIT 1");
    $num_rows = mysql_num_rows($result);
    if(!$num_rows ||$num_rows <1){
        aktuel_sms_activate();
    }
    $settings = mysql_fetch_array($result);
    $apiparams = json_decode($settings['apiparams']);

    /* CUSTOM FIELDS START */
    $where = array(
        "fieldtype" => array("sqltype" => "LIKE", "value" => "tickbox"),
        "showorder" => array("sqltype" => "LIKE", "value" => "on")
    );
    $result = select_query("tblcustomfields", "id,fieldname", $where);
    while ($data = mysql_fetch_array($result)) {
        if ($data['id'] == $settings['wantsmsfield']) {
            $selected = 'selected="selected"';
        } else {
            $selected = "";
        }
        $wantsms .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['fieldname'] . '</option>';
    }

    $where = array(
        "fieldtype" => array("sqltype" => "LIKE", "value" => "text"),
        "showorder" => array("sqltype" => "LIKE", "value" => "on")
    );
    $result = select_query("tblcustomfields", "id,fieldname", $where);
    while ($data = mysql_fetch_array($result)) {
        if ($data['id'] == $settings['gsmnumberfield']) {
            $selected = 'selected="selected"';
        } else {
            $selected = "";
        }
        $gsmnumber .= '<option value="' . $data['id'] . '" ' . $selected . '>' . $data['fieldname'] . '</option>';
    }
    /* CUSTOM FIELDS FINISH HIM */

    echo '
    <form action="" method="post">
    <input type="hidden" name="action" value="save" />
	    <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 10px;">
		    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
			    <tbody>

                    <tr>
					    <td class="fieldlabel" width="30%">WHMCS Path</td>
						<td class="fieldarea"><input type="text" name="path" size="40" value="' . $settings['path'] . '"> e.g:  /home/aktuelhost/public_html/</td>
					</tr>
			        <tr>
			            <td class="fieldlabel" width="30%">Sender</td>
						<td class="fieldarea">
						    <select name="api">
						        <option value="ClickAtell" ' . (($settings['api'] == "ClickAtell")?"selected=\"selected\"":"") . '>ClickAtell</option>
                                			<option value="IletiMerkezi" ' . (($settings['api'] == "IletiMerkezi")?"selected=\"selected\"":"") . '>Ileti Merkezi</option>
						        <option value="NetGsm" ' . (($settings['api'] == "NetGsm")?"selected=\"selected\"":"") . '>NetGsm</option>
						        <option value="UcuzSmsAl" ' . (($settings['api'] == "UcuzSmsAl")?"selected=\"selected\"":"") . '>UcuzSmsAl</option>
						    </select>
						</td>
			        </tr>
				    <tr>
					    <td class="fieldlabel" width="30%">SenderID</td>
						<td class="fieldarea"><input type="text" name="params[senderid]" size="40" value="' . $apiparams->senderid . '"> e.g:  AktuelHost</td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">Username</td>
						<td class="fieldarea"><input type="text" name="params[user]" size="40" value="' . $apiparams->user . '"></td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">Password</td>
						<td class="fieldarea"><input type="text" name="params[pass]" size="40" value="' . $apiparams->pass . '"></td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">API ID</td>
						<td class="fieldarea"><input type="text" name="params[apiid]" size="40" value="' . $apiparams->apiid . '"></td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">Want SMS Field</td>
						<td class="fieldarea">
						    <select name="wantsmsfield">
						        ' . $wantsms . '
						    </select>
						</td>
					</tr>

					<tr>
					    <td class="fieldlabel" width="30%">GSM Number Field</td>
						<td class="fieldarea">
						    <select name="gsmnumberfield">
						        ' . $gsmnumber . '
						    </select>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<p align="center"><input type="submit" value="Save Changes" class="button" /></p>
    </form>
    ';
}
elseif ($tab == "templates")
{

    if ($_POST['submit']) {
        $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
        $result = select_query("mod_aktuelsms_templates", "*", $where);
        while ($data = mysql_fetch_array($result)) {
            if ($_POST[$data['id'] . '_active'] == "on") {
                $tmp_active = 1;
            } else {
                $tmp_active = 0;
            }
            $update = array(
                "template" => $_POST[$data['id'] . '_template'],
                "active" => $tmp_active
            );

            if(isset($_POST[$data['id'] . '_extra'])){
                $update['extra']= trim($_POST[$data['id'] . '_extra']);
            }
            if(isset($_POST[$data['id'] . '_admingsm'])){
                $update['admingsm']= $_POST[$data['id'] . '_admingsm'];
                $update['admingsm'] = str_replace(" ","",$update['admingsm']);
            }
            update_query("mod_aktuelsms_templates", $update, "id = " . $data['id']);
        }
    }

    echo '<form action="" method="post">
    <input type="hidden" name="action" value="save" />
	    <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 10px;">
		    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
			    <tbody>';
    $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
    $result = select_query("mod_aktuelsms_templates", "*", $where);
    while ($data = mysql_fetch_array($result)) {
        if ($data['active'] == 1) {
            $active = 'checked = "checked"';
        } else {
            $active = '';
        }
        echo '
            <tr>
		        <td class="fieldlabel" width="30%">' . $data['name'] . '</td>
				<td class="fieldarea">
				    <textarea cols="50" name="' . $data['id'] . '_template">' . $data['template'] . '</textarea>
				</td>
			</tr>';
        echo '
        <tr>
            <td class="fieldlabel" width="30%" style="float:right;">Active?</td>
            <td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>
        </tr>
        ';
        echo '
        <tr>
            <td class="fieldlabel" width="30%" style="float:right;">Variables:</td>
            <td>' . $data['variables'] . '</td>
        </tr>
        ';

        if(!empty($data['extra'])){
            echo '
            <tr>
		        <td class="fieldlabel" width="30%">Extra</td>
				<td class="fieldarea">
				    <input type="text" name="'.$data['id'].'_extra" value="'.$data['extra'].'">
				</td>
			</tr>
            ';
        }
        if($_GET['type'] == "admin"){
            echo '
            <tr>
		        <td class="fieldlabel" width="30%">Admin Gsm Numbers</td>
				<td class="fieldarea">
				    <input type="text" name="'.$data['id'].'_admingsm" value="'.$data['admingsm'].'">
				    Seperate with comma. e.g: 5321234567.5321234568
				</td>
			</tr>
            ';
        }
    }
    echo '
    </tbody>
			</table>
		</div>
		<p align="center"><input type="submit" name="submit" value="Save Changes" class="button" /></p>
    </form>';

}
elseif ($tab == "messages")
{
    if(!empty($_GET['deletesms'])){
        $smsid = (int) $_GET['deletesms'];
        $sql = "DELETE FROM mod_aktuelsms_messages WHERE id = '$smsid'";
        mysql_query($sql);
    }
    echo '
    <table class="datatable" width="70%" border="0" cellspacing="1" cellpadding="3">
        <tr>
            <th>User</th>
            <th>Gsm No</th>
            <th>Message</th>
            <th>Date Time</th>
            <th>Status</th>
            <th width="20"></th>
        </tr>';

    /* Getting messages order by date desc */
    $sql = "SELECT `messages`.`id`,`messages`.`to`,`messages`.`text`,`messages`.`datetime`,`messages`.`user`,`user`.`firstname`,`user`.`lastname`
    FROM `mod_aktuelsms_messages` as `messages`
    JOIN `tblclients` as `user` ON `messages`.`user` = `user`.`id`
    ORDER BY `messages`.`datetime` DESC";
    $result = mysql_query($sql);
    while ($data = mysql_fetch_array($result)) {
        echo '<tr>
        <td><a href="clientssummary.php?userid='.$data['user'].'">'.$data['firstname'].' '.$data['lastname'].'</a></td>
        <td>'.$data['to'].'</td>
        <td>'.$data['text'].'</td>
        <td>'.$data['datetime'].'</td>
        <td><!--<div align="center"><img src="images/icons/tick.png"></div>--></td>
        <td><a href="addonmodules.php?module=aktuel_sms&tab=messages&deletesms='.$data['id'].'" title="Delete Message"><img src="images/delete.gif" width="16" height="16" border="0" alt="Delete"></a></td></tr>';
    }

    /* Getting messages order by date desc */

    echo '
    </table>
    ';

}
elseif($tab=="sendbulk")
{

    $result = select_query("mod_aktuelsms_settings", "*");
    $settings = mysql_fetch_array($result);
    include_once($settings['path']."modules/admin/aktuel_sms/smsclass.php");

    if(!empty($_POST['client'])){

        $userinf = explode("_",$_POST['client']);
        $userid = $userinf[0];
        $gsmnumber = $userinf[1];
        $send = new SendGsm();
        $send->sender = $settings['api'];
        $send->params = $settings['apiparams'];
        $send->gsmnumber = $gsmnumber;
        $send->message = $_POST['message'];
        $send->userid = $userid;
        $send->send();


        if(count($send->errors) > 0){
            echo '<ul>';
            foreach($send->errors as $error){
                echo "<li>$error</li>";
            }
            echo '</ul>';

        }else{
            echo 'SMS Sent to '.$gsmnumber;
        }

        if($_POST["debug"] == "ON"){
            $debug = $send->logs;
        }else{
            $debug = null;
        }

    }

    $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `b`.`value` as `gsmnumber`
	FROM `tblclients` as `a`
	JOIN `tblcustomfieldsvalues` as `b` ON `b`.`relid` = `a`.`id`
	JOIN `tblcustomfieldsvalues` as `c` ON `c`.`relid` = `a`.`id`
	WHERE `b`.`fieldid` = '".$settings['gsmnumberfield']."'
	AND `c`.`fieldid` = '".$settings['wantsmsfield']."'
	AND `c`.`value` = 'on'";
    $result = mysql_query($userSql);
    while ($data = mysql_fetch_array($result)) {
        $clients .= '<option value="'.$data['id'].'_'.$data['gsmnumber'].'">'.$data['firstname'].' '.$data['lastname'].' (#'.$data['id'].')</option>';
    }

    echo '<form action="" method="post">
    <input type="hidden" name="action" value="save" />
	    <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 10px;">
		    <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3">
			    <tbody>
					<tr>
					    <td class="fieldlabel" width="30%">Client</td>
						<td class="fieldarea">
						    <select name="client">
						        <option value="">Select a Client</option>
						        ' . $clients . '
						    </select>
						</td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">Message</td>
						<td class="fieldarea">
                            <textarea cols="50" name="message"></textarea>
						</td>
					</tr>
					<tr>
					    <td class="fieldlabel" width="30%">Debug</td>
					    <td class="fieldlabel"><input type="checkbox" name="debug" value="ON"></td>
					</tr>
				</tbody>
			</table>
		</div>
		<p align="center"><input type="submit" value="Send" class="button" /></p>
    </form>';

    if(isset($debug) and $debug != null){

        echo '<p><strong>Debug result:</strong><ul>';
        foreach($debug as $d){
            echo "<li>$d</li>";
        }
        echo '</ul></p>';
    }
}

    echo 'Plugin by <a href="http://www.aktuelsistem.com/">Aktüel Sistem ve Bilgi Teknolojileri</a>';
