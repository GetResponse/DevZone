#INFO

The phpBB plugin allows you to subscribe new contacts into your GetResponse Email Marketing campaign whenever they created an account at your forum. Being added into a campaign requires to enable to appropriate checkbox.

---

#AUTHOR

**Paweł Ługowski**

---

#REQUIRED

phpBB admin account and access to the files.

![Create account](https://dl.dropboxusercontent.com/u/21062041/phpBB2.png)

---

#INSTALLATION

Download the module **plugin_phpBB.zip**

The installation requires from you two steps:

1. Copy process:
Copy: root/language/en/*.*         
To: language/en/*.*      

Copy: root/includes/*.*         
To: includes/*.*      

Copy: root/files/jsonRPCClient.php         
To: files/jsonRPCClient.php

2. Edition of the code in selected files
Afterwards you will need to modify certain files, as described in the install.xml.

---

#SUBSCRIPTION VIA THE REGISTRATION FORM

You will want to decide into what campaign to add new contacts. Please first enter the the API key into appropriate input field at the General Settings page under the Boad Configuration >> User registration settings. Click on Submit to confirm your entry.

![Create account](https://dl.dropboxusercontent.com/u/21062041/phpBB2.png)

Now get back onto the same page, as described above and choose the destination campaign where to add new contacts to.

If you wish to add the „ Select if you want to sign up for the newsletter“ checkbox on your forum registration page, please activate the „Enable GetResponse Subscription“ radio button and hit SUBMIT.

