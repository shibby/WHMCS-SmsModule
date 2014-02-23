TURKISH
=======

WHMCS Sms Eklentisi
---------------

WHMCS için açık kaynak kodlu, SMS gönderme eklentisidir. Eklenti ücretsizdir.

Kurulum
---------------

* Dosyaları WHMCS klasörünüze yükleyin.
* Admin sayfasına gidin. Menu->Setup->Addon Modules (Menü->Ayarlar->Ek Modüller) sayfasına gidin ve Aktuel Sms eklentisini aktifleştirin.
* Kaydettikten sonra, aynı sayfada admin gruplarına yetki vermeyi unutmayın.
* Menu->Setup->Custom Client Fields (Menü->Ayarlar->Müşteri Alanları) sayfasına gidin.
* Yeni Bir Alan Ekleyin: name=Mesaj almak istiyor musunuz?, type= Tick box, Show on Order Form=check. (Bu alan, müşteriye kayıt sayfasında gösterilecek. Müşteri bu alanı işaretlemezse, sms gönderilmeyecek)
* Yeni bir alan ekleyin: name=GSM Numarası, type=Text Box, Show on Order Form=check. (Bu alan, müşteriye kayıt sayfasında gösterilecek. Sms, müşterinin buraya girdiği değere gönderilecek)

* Menu->Addons->Aktuel Sms (Menü->Eklentiler->Aktuel Sms) Eklentinin sayfasına gidin.
* WHMCS yolunuzu yazın ve SMS göndermek için gönderici seçin. Daha sonra api detaylarınızı yazın.


Desteklenen SMS Firmaları
----------------------

* ClickAtell (Global)
* NetGsm (Türkiye)
* UcuzSmsAl (Türkiye)
* Mutlucell (Türkiye)
* Dakik SMS (Türkiye)
* msg91.com (Hindistan)
* bytehand.com (Global, Russian)


Desteklenen Hook'lar
---------------

* ClientChangePassword: Müşterinin şifresi değiştiğinde sms gönderilir.
* TicketAdminReply: Admin, müşterinin destek talebini yanıtladığında, müşteriye sms gönderilir.
* ClientAdd: Müşteri kayıt olduğunda, hoşgeldiniz mesajı gönderilir.
* AfterRegistrarRegistration: Domain başarıyla kayıt olduğunda, müşteriye bilgi mesajı gönderilir.
* AfterRegistrarRenewal: Domain başarıyla yenilendiğinde, müşteriye bilgi mesajı gönderilir.
* AfterModuleCreate_SharedAccount: Hosting hesabı oluşturulduğunda müşteriye kullanıcı adı ve şifrelerle birlikte bilgi mesajı gönderilir.
* AfterModuleCreate_ResellerAccount: Reseller hesabı oluşturulduğunda müşteriye kullanıcı adı ve şifrelerle birlikte bilgi mesajı gönderilir.
* AcceptOrder: Müşterinin siparişi onaylandığında, müşteriye bilgi mesajı gönderilir.
* DomainRenewalNotice: Domainin süresinin dolmasına {x} gün kala müşteriye bilgilendirme mesajı gönderilir. {x: Eklentide belirleyebilisiniz.)
* InvoicePaymentReminder: Eğer ödenmemiş bir fatura varsa müşteriye bilgi mesajı gönderilir.
* InvoicePaymentReminder_FirstOverdue: Eğer fatura ödemesinin günü geçtiyse müşteriye bilgi mesajı gönderilir.
* InvoicePaymentReminder_secondoverdue: Ödenmemiş faturanın ikinci zaman aşımında mesaj gönderir.
* InvoicePaymentReminder_thirdoverdue: Ödenmemiş faturanın üçüncü zaman aşımında mesaj gönderir.
* AfterModuleSuspend: Hosting hesabı suspend edilirse bilgi mesajı gönderilir. 
* AfterModuleUnsuspend: Hosting hesabı unsuspend edilirse bilgi mesajı gönderilir. 
* InvoiceCreated: Sistem yeni fatura oluşturursa bilgi mesajı gönderilir. 
* AfterModuleChangePassword: Hosting hesabı şifresi değiştiğinde gönderir.
* InvoicePaid: Faturanız ödendiğinde mesaj gönderir.

Katkıda bulun
---------------

Yeni hook, fonksiyon, sms gönderici ve diğer bütün herşeyde özgürsünüz. Eklentiyi çoğaltın (fork) ve yaptığınız değişiklikler için Pull Request gönderin.

Katkıda bulunanlar
----------

* [Güven Atbakan](http://github.com/shibby) - PHP Geliştirici  [Aktüel Sistem ve Bilgi Teknolojileri](http://www.aktuelsistem.com) - guven[dot]atbakan[at]aktuelsistem[dot]com
* [Turgay Coşkun](http://github.com/adalim61) - turgaycoskun[at]gmail[dot]com

Bazı Ekran Görüntüleri
--------------

[![General Settings](http://i.imgur.com/ai5e1hos.png)](http://i.imgur.com/ai5e1ho.png)
[![Sms Templates](http://i.imgur.com/PUksoY9s.png)](http://i.imgur.com/PUksoY9.png)
[![Send Manual Sms](http://i.imgur.com/EJNwpwIs.png)](http://i.imgur.com/EJNwpwI.png)


ENGLISH
=======

WHMCS Sms Module
---------------

Open Source SMS Module for WHMCS Automation.

Installation
---------------

* Upload files to your WHMCS root.
* Go to Admin Area. Enter Menu->Setup->Addon Modules and Activate Aktuel Sms
* After saving changes, give privigle to admin groups that you want at same page.
* Go to Menu->Setup->Custom Client Fields
* Add a field: name=Send Sms, type= Tick box, Show on Order Form=check. (This field will be shown at register page. If user do not check this field, SMS will not send to this user)
* Add a field: name=GSM Number, type=Text Box, Show on Order Form=check. (This field will be shown at register page. Sms will send to this value that user fills.)

* Enter Menu->Addons->Aktuel Sms
* Write WHMCS Path and Select SMS Gateway. Write your api details.


Supported SMS Gateways
---------------

* ClickAtell (Global)
* NetGsm (Turkey)
* UcuzSmsAl (Turkey)
* Mutlucell (Turkey)
* Dakik SMS (Turkey)
* msg91.com (India)
* bytehand.com (Global, Russian)

Supported Hooks
---------------

* ClientChangePassword: Send sms to user if changes account password
* TicketAdminReply: Send sms to user if admin replies user's ticket
* ClientAdd: Send sms when user register
* AfterRegistrarRegistration: Send sms to user when domain registred succesfully
* AfterRegistrarRenewal: Send sms to user when domain renewed succesfully
* AfterModuleCreate_SharedAccount: Send sms to user when hosting account created.
* AfterModuleCreate_ResellerAccount: Send sms to user when reseller account created.
* AcceptOrder: Send sms to user when order accepted manually or automatically.
* DomainRenewalNotice: Remaining to the end of {x} days prior to the domain's end time, user will be get a message.
* InvoicePaymentReminder: If there is a payment that not paid, user will be get a information message.
* InvoicePaymentReminder_FirstOverdue: Invoice payment first for seconds overdue.
* InvoicePaymentReminder_secondoverdue: Invoice payment second for seconds overdue.
* InvoicePaymentReminder_thirdoverdue: Invoice payment third for seconds overdue.
* AfterModuleSuspend: Send sms after hosting account suspended. 
* AfterModuleUnsuspend: Send sms after hosting account unsuspended.
* InvoiceCreated: Send sms every invoice creation. 
* AfterModuleChangePassword: After module change password.
* InvoicePaid: Whenyou have paidthe billsends a message.

Contribute Plugin
---------------

You are free (as freedom) to add new hooks, functions, issues, gateways etc. Just fork plugin, change what do you want and send pull request.

Contributors
----------

* [Guven Atbakan](http://github.com/shibby) - PHP Developer at [Aktuel Sistem ve Bilgi Teknolojileri](http://www.aktuelsistem.com) - guven[dot]atbakan[at]aktuelsistem[dot]com
* [Turgay Coşkun](http://github.com/adalim61) - turgaycoskun[at]gmail[dot]com
