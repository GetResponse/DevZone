#GetResponse Callback

version 1.2.2, 2014-04-30 [changelog](#changelog)

##GETTING STARTED

Callbacks allow external systems to be notified about GetResponse contact activities.

####Configuration

Callbacks can be managed using API [get_account_callbacks](https://github.com/GetResponse/DevZone/tree/master/API/README.md#get_account_callbacks), [set_account_callbacks](https://github.com/GetResponse/DevZone/tree/master/API/README.md#set_account_callbacks) and [delete_account_callbacks](https://github.com/GetResponse/DevZone/tree/master/API/README.md#delete_account_callbacks) methods or on [web interface](https://app.getresponse.com/my_api_key.html).

We support SSL, so you can use either HTTP or HTTPS for callback messages.

####Parameters

Sample HTTP callback looks like this:

```
http://foo.bar/callback?action=open \
  &ACCOUNT_ID=A1&account_login=myaccount \
  &CAMPAIGN_ID=C1&campaign_name=mycampaign \
  &MESSAGE_ID=M1&message_name=My%20message&message_subject=Some%20subject \
  &CONTACT_ID=C1&contact_name=Friend&contact_email=friend@implix.com
```

All `*_ID` params are compatible with corresponding identifiers in [API](https://github.com/GetResponse/DevZone/tree/master/API/README.md).
So for example after receiving [open](#open) callback one may call [get_message](https://github.com/GetResponse/DevZone/tree/master/API/README.md#get_message) API method with `MESSAGE_ID` from callback
to fetch additional information about opened message. Those params are case-sensitive.

####Failures

Callback timeout is 4 seconds, not received callbacks ***are lost and will not be repeated***.

Make sure that target server is capable of handling expected amount of requests, especially when SSL URI is given which is more CPU-intensive.

##SUPPORT

The GetResponse Callback interface is created and maintained by the *GetResponse DevZone Team*.

If you run into an error or you have difficulties with using Callbacks please contact us using [this form](http://www.getresponse.com/feedback.html?devzone=yes) and we will provide all the support we can to solve your problems.

##CALLS

* [subscribe](#subscribe)
* [open](#open)
* [click](#click)
* [goal](#goal)
* [survey](#survey)
* [unsubscribe](#unsubscribe)

---

####subscribe<a name="subscribe"/>

_Params:_

* `action` - Always `subscribe`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `CONTACT_ID`
* `contact_name` (optional)
* `contact_email`
* `contact_ip` (optional) - In case of double-optin this is the IP than confirmation link was clicked from.
* `contact_origin` - One of `import`, `email`, `www`, `panel`, `leads`, `sale`, `api`, `forward`, `survey`, `iphone`, `copy`, `landing_page`.

---

####open<a name="open"/>

_Params:_

* `action` - Always `open`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `MESSAGE_ID`
* `message_name`
* `message_subject`
* `CONTACT_ID`
* `contact_name` (optional)
* `contact_email`

---

####click<a name="click"/>

_Params:_

* `action` - Always `click`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `MESSAGE_ID`
* `message_name`
* `message_subject`
* `LINK_ID`
* `link_url`
* `CONTACT_ID`
* `contact_name` (optional)
* `contact_email`

---

####goal<a name="goal"/>

_Params:_

* `action` - Always `goal`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `GOAL_ID`
* `goal_profile`
* `goal_domain`
* `goal_name`
* `goal_url`
* `CONTACT_ID`
* `contact_name` (optional)
* `contact_email`
* `goal_param_0`
* `goal_param_1` (optional)
* `goal_param_2` (optional)
* `goal_param_3` (optional)
* `goal_param_4` (optional)
* `goal_param_5` (optional)

---

####survey<a name="survey"/>

_Params:_

* `action` - Always `survey`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `MESSAGE_ID` (optional) - Present if survey was sent in email message.
* `message_name` (optional) - Present if survey was sent in email message.
* `message_subject` (optional) - Present if survey was sent in email message.
* `SURVEY_ID`
* `survey_name`
* `survey_title` (optional)
* `QUESTION_ID`
* `question_name`
* `question_answer` (optional) - Present if question is of text type.
* `OPTION_ID` (optional) - Present if question has predetermined options to select.
* `option_name` (optional) - Present if question has predetermined options to select.
* `CONTACT_ID` (optional) - Present if survey was sent in email message.
* `contact_name` (optional) - Present if survey was sent in email message.
* `contact_email` (optional) - Present if survey was sent in email message.

**Note**: Every answer generates separate callback. So if you have survey with one text question, one question with single select answer and one question with three multi select answers you may receive up to 5 callbacks for one contact filling this survey. It also means that survey does not have to be completed by contact to generate callbacks.

---

####unsubscribe<a name="unsubscribe"/>

_Params:_

* `action` - Always `unsubscribe`.
* `ACCOUNT_ID`
* `account_login`
* `CAMPAIGN_ID`
* `campaign_name`
* `CONTACT_ID`
* `contact_name` (optional)
* `contact_email`

**Warning**: This callback is generated only when contact use unsubscribe link. Other removal reasons such as bounces of complaints are not reported through this callback.

##CHANGELOG<a name="changelog">

version 1.2.2, 2014-04-30

* [subscribe](#subscribe) has updated origins

version 1.2.1, 2012-11-30

* [survey](#survey) callback has `question_answer` field added for text answers

version 1.2.0, 2012-11-29

* survey params in [survey](#survey) callback

version 1.1.0, 2012-11-12

* goal params in [goal callback](#goal)

version 1.0.0, 2012-11-06

* initial release
