Souce code is coming soon.

WHMCS Sms Module
===============

Open Source SMS Module for WHMCS Automation.

Installation
============

* Upload files to your WHMCS root.
* Go to Admin Area. Enter Menu->Setup->Addon Modules and Activate Aktuel Sms
* After saving changes, give privigle to admin groups that you want at same page.
* Go to Menu->Setup->Custom Client Fields
* Add a field: name=Send Sms, type= Tick box, Show on Order Form=check. (This field will be shown at register page. If user do not check this field, SMS will not send to this user)
* Add a field: name=GSM Number, type=Text Box, Show on Order Form=check. (This field will be shown at register page. Sms will send to this value that user fills.)

* Enter Menu->Addons->Aktuel Sms 
* Write WHMCS Path and Select SMS Gateway. Write your api details.


Supported SMS Gateways
----------------------

* ClickAtell (Global)
* NetGsm (Turkey)


Supported Hooks
---------------

* ClientChangePassword: Send sms to user if changes account password
* TicketAdminReply: Send sms to user if admin replies user's ticket
* ClientAdd: Send sms when user register
* AfterRegistrarRegistration: Send sms to user when domain registred succesfully
* AfterRegistrarRenewal: Send sms to user when domain renewed succesfully

Contribute Plugin
=================

You are free (as freedom) to add new hooks, functions, issues, gateways etc. Just fork plugin, change what do you want and send pull request. 

Developers
----------

* [Guven Atbakan](http://github.com/shibby) - PHP Developer at [Aktuel Sistem ve Bilgi Teknolojileri](http://www.aktuelsistem.com) - guven[dot]atbakan[at]aktuelsistem[dot]com
