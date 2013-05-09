DROP TABLE mod_aktuelsms_messages;
CREATE TABLE IF NOT EXISTS `mod_aktuelsms_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`sender` varchar(40) NOT NULL,`to` varchar(15) NOT NULL,`text` text NOT NULL,`msgid` varchar(50) NOT NULL,`status` varchar(10) NOT NULL,`user` int(11) NOT NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

DROP TABLE mod_aktuelsms_settings;
CREATE TABLE IF NOT EXISTS `mod_aktuelsms_settings` ( `id` int(11) NOT NULL AUTO_INCREMENT,`api` varchar(40) NOT NULL,`apiparams` varchar(100) NOT NULL,`wantsmsfield` int(11) NOT NULL,`gsmnumberfield` int(11) NOT NULL,`path` varchar(100) NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2;
INSERT INTO `mod_aktuelsms_settings` (`api`, `apiparams`, `wantsmsfield`, `gsmnumberfield`, `path`) VALUES ('', '', 0, 0, '');

DROP TABLE mod_aktuelsms_templates;
CREATE TABLE IF NOT EXISTS `mod_aktuelsms_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` enum('client','admin') NOT NULL,
  `admingsm` varchar(60) NOT NULL,
  `template` varchar(240) NOT NULL,
  `variables` varchar(500) NOT NULL,
  `active` tinyint(1) NOT NULL,
  `extra` varchar(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;


INSERT INTO `mod_aktuelsms_templates` (`id`, `name`, `type`, `admingsm`, `template`, `variables`, `active`, `extra`) VALUES
(1, 'TicketAdminReply', 'client', '', 'Sayin {firstname} {lastname}, ({ticketsubject}) konu baslikli destek talebiniz yanitlandi.', '{firstname},{lastname},{ticketid},{ticketsubject}', 1, ''),
(2, 'ClientChangePassword', 'client', '', 'Sayin {firstname} {lastname}, sifreniz degistirildi. Eger bu islemi siz yapmadiysaniz lutfen bizimle iletisime gecin.', '{firstname},{lastname}', 1, ''),
(3, 'ClientAdd', 'client', '', 'Sayin {firstname} {lastname}, AktuelHost a kayit oldugunuz icin tesekkur ederiz. Email: {email} Sifre: {password}', '{firstname},{lastname},{email},{password}', 1, ''),
(4, 'AfterRegistrarRegistration', 'client', '', 'Sayin {firstname} {lastname}, alan adiniz basariyla kayit edildi. ({domain})', '{firstname},{lastname},{domain}', 1, ''),
(5, 'AfterRegistrarRenewal', 'client', '', 'Sayin {firstname} {lastname}, alan adiniz basariyla yenilendi. ({domain})', '{firstname},{lastname},{domain}', 1, ''),
(6, 'AfterModuleCreate_SharedAccount', 'client', '', 'Sayin {firstname} {lastname}, {domain} icin hosting hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, ''),
(9, 'AcceptOrder', 'client', '', 'Sayin {firstname} {lastname}, {orderid} numarali siparisiniz onaylanmistir. ', '{firstname},{lastname},{orderid}', 0, ''),
(7, 'AfterModuleCreate_ResellerAccount', 'client', '', 'Sayin {firstname} {lastname}, {domain} icin reseller hizmeti aktif hale getirilmistir. KullaniciAdi: {username} Sifre: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, ''),
(10, 'DomainRenewalNotice', 'client', '', 'Sayin {firstname} {lastname}, {domain} alanadiniz {expirydate}({x} gun sonra) tarihinde sona erecektir. Yenilemek icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {domain},{x}', 1, '15'),
(11, 'InvoicePaymentReminder', 'client', '', 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {duedate}', 1, ''),
(12, 'InvoicePaymentReminder_FirstOverdue', 'client', '', 'Sayin {firstname} {lastname}, {duedate} son odeme tarihli bir faturaniz bulunmaktadir. Detayli bilgi icin sitemizi ziyaret edin. www.aktuelhost.com', '{firstname}, {lastname}, {duedate}', 1, '');
