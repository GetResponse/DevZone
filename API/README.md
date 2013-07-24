#GetResponse API

version 1.31.0, 2013-07-24 [changelog](#changelog)

##GETTING STARTED

####Authentication

![API KEY](https://d3p2zxbkhhu56x.cloudfront.net/images/core/global/default/icons/use-getresponse-api_1.png)

In order to use GetResponse API the API KEY is required.

* GetResponse users can obtain it from [my account](https://app.getresponse.com/my_api_key.html) section after logging to your GetResponse account.
* GetResponse360 users should login to server with owner privileges and visit "My account" → "Use GetResponse API".

**Warning**: Please note that the API KEY unambiguously identifies your account and allows all who know the API KEY to manage contacts, messages etc. Please keep your API KEY safe and do not share it with any unauthorized persons.

---

####Protocol

GetResponse API is [JSON-RPC](http://www.jsonrpc.org/) based and supports both 1.0 and 2.0 (with Notifications and Batches) specifications.

**Warning**: 1.1 draft specification support will be dropped by the end of the year, please adjust your code/libraries to use official 1.0 or 2.0 specification.

**Warning**: If you're receiving **HTTP "204 No content"** response check if `id` member is present in your Requests. Lack of this member means that the Request is a Notification - signifies the Clients lack of interest in receiving Response as explained [here](http://www.jsonrpc.org/specification#notification). Our API supports Notification requests since November 30th. If your integration due to lack of `id` member requested this functionality by mistake then don't worry because all Notification Requests were processed by our servers. If you used for example add_contact() method those subscriptions are not lost.

**Note**: We support SSL and gzipped transfer (over 1KB).

---

####Location

* GetResponse users should use `http://api2.getresponse.com` URL.
* GetResponse360 users have unique URL and it will be provided to them by Account Manager.

---

####Examples

Check how to use API in following programming languages in our [examples](https://github.com/GetResponse/DevZone/tree/master/API/examples) section.

<img src="http://upload.wikimedia.org/wikipedia/commons/7/72/Logo_C_Sharp.png" height="64" title="C#" alt="C#"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Java_logo_and_wordmark.svg/200px-Java_logo_and_wordmark.svg.png" height="64" title="Java" alt="Java"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/thumb/6/6a/JavaScript-logo.png/600px-JavaScript-logo.png" height="64" title="JavaScript" alt="JavaScript"/>
<img src="http://upload.wikimedia.org/wikipedia/en/0/00/Perl-camel-small.png" height="64" title="Perl" alt="Perl"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/thumb/2/27/PHP-logo.svg/220px-PHP-logo.svg.png" height="64" title="PHP" alt="PHP"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Python-logo-notext.svg/110px-Python-logo-notext.svg.png" height="64" title="Python" alt="Python"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Ruby_logo.svg/198px-Ruby_logo.svg.png" height="64" title="Ruby" alt="Ruby"/>
<img src="http://upload.wikimedia.org/wikipedia/commons/8/8f/Adobe_Flex.png" height="64" title="AdobeFlex" alt="Adobe Flex"/>

There are also PHP and Ruby wrappers available in our [wrappers](https://github.com/GetResponse/DevZone/tree/master/API/wrappers) section.

##SUPPORT

The GetResponse API is created and maintained by the *GetResponse DevZone Team*.

If you run into an error or you have difficulties with using the API please contact us using [this form](http://www.getresponse.com/feedback.html?devzone=yes) and we will provide all the support we can to solve your problems.


##METHODS

####Connection testing

* [ping](#ping)

####Account

* [get_account_info](#get_account_info)
* [get_account_from_fields](#get_account_from_fields)
* [get_account_from_field](#get_account_from_field)
* [add_account_from_field](#add_account_from_field)
* [get_account_domains](#get_account_domains)
* [get_account_domain](#get_account_domain)
* [get_account_customs](#get_account_customs)
* [add_account_custom](#add_account_custom)
* [set_account_custom_contents](#set_account_custom_contents)
* [delete_account_custom](#delete_account_custom)

####Campaigns

* [get_campaigns](#get_campaigns)
* [get_campaign](#get_campaign)
* [add_campaign](#add_campaign)
* [get_campaign_domain](#get_campaign_domain)
* [set_campaign_domain](#set_campaign_domain)
* [delete_campaign_domain](#delete_campaign_domain)
* [get_campaign_postal_address](#get_campaign_postal_address)
* [set_campaign_postal_address](#set_campaign_postal_address)

####Messages

* [get_messages](#get_messages)
* [get_message](#get_message)
* [get_message_contents](#get_message_contents)
* [get_message_stats](#get_message_stats)
* [send_newsletter](#send_newsletter)
* [delete_newsletter](#delete_newsletter)
* [add_autoresponder](#add_autoresponder)
* [set_autoresponder_cycle](#set_autoresponder_cycle)
* [delete_autoresponder](#delete_autoresponder)
* [add_draft](#add_draft)
* [get_messages_amount_per_account](#get_messages_amount_per_account)
* [get_messages_amount_per_campaign](#get_messages_amount_per_campaign)
* [get_newsletter_statuses](#get_newsletter_statuses)

####Contacts

* [get_contacts](#get_contacts)
* [get_contact](#get_contact)
* [set_contact_name](#set_contact_name)
* [get_contact_customs](#get_contact_customs)
* [set_contact_customs](#set_contact_customs)
* [get_contact_geoip](#get_contact_geoip)
* [get_contact_opens](#get_contact_opens)
* [get_contact_clicks](#get_contact_clicks)
* [get_contact_goals](#get_contact_goals)
* [get_contact_surveys](#get_contact_surveys)
* [set_contact_cycle](#set_contact_cycle)
* [add_contact](#add_contact)
* [move_contact](#move_contact)
* [delete_contact](#delete_contact)
* [get_contacts_deleted](#get_contacts_deleted)
* [get_contacts_subscription_stats](#get_contacts_subscription_stats)
* [get_contacts_amount_per_account](#get_contacts_amount_per_account)
* [get_contacts_amount_per_campaign](#get_contacts_amount_per_campaign)
* [get_contacts_distinct_amount](#get_contacts_distinct_amount)
* [get_segments](#get_segments)

####Links

* [get_links](#get_links)
* [get_link](#get_link)

####Goals

* [get_goals](#get_goals)
* [get_goal](#get_goal)

####Surveys

* [get_surveys](#get_surveys)
* [get_survey](#get_survey)
* [get_survey_stats](#get_survey_stats)

####Blacklists

* [get_account_blacklist](#get_account_blacklist)
* [add_account_blacklist](#add_account_blacklist)
* [delete_account_blacklist](#delete_account_blacklist)
* [get_campaign_blacklist](#get_campaign_blacklist)
* [add_campaign_blacklist](#add_campaign_blacklist)
* [delete_campaign_blacklist](#delete_campaign_blacklist)

####Suppressions

* [get_suppressions](#get_suppressions)
* [get_suppression](#get_suppression)
* [add_suppression](#add_suppression)
* [delete_suppression](#delete_suppression)
* [get_suppression_skiplist](#get_suppression_skiplist)
* [add_suppression_skiplist](#add_suppression_skiplist)
* [delete_suppression_skiplist](#delete_suppression_skiplist)

####Confirmation

* [get_confirmation_subjects](#get_confirmation_subjects)
* [get_confirmation_subject](#get_confirmation_subject)
* [get_confirmation_bodies](#get_confirmation_bodies)
* [get_confirmation_body](#get_confirmation_body)

####Callbacks

* [get_account_callbacks](#get_account_callbacks)
* [set_account_callbacks](#set_account_callbacks)
* [delete_account_callbacks](#delete_account_callbacks)


####Server (GetResponse360 only)

* [add_account](#add_account)
* [get_accounts](#get_accounts)
* [get_account](#get_account)
* [set_account_status](#set_account_status)

---

####ping<a name="ping"/>

Test connection with API.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "ping" : "pong"
    }
```

---

####get_account_info<a name="get_account_info"/>

Get basic info about your account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "login"         : "my_login",
        "from_name"     : "My From Name",
        "from_email"    : "me@emailaddress.com",
        "created_on"    : "2010-01-01"
    }
```

---

####get_account_from_fields<a name="get_account_from_fields"/>

Get list of email addresses assigned to account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "FROM_FIELD_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "email"         : "me@emailaddress.com",
            "name"          : "My From Name"
        },
        "FROM_FIELD_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "email"         : "also.me@another-emailaddress.com",
            "name"          : "My Other Name"
        }
    }
```

---

####get_account_from_field<a name="get_account_from_field"/>

Get single email address assigned to account using `FROM_FIELD_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "account_from_field"    : "FROM_FIELD_ID"
        }
    ]
```
Conditions:

* `account_from_field` (mandatory) – `FROM_FIELD_ID`.

_JSON response:_

```json
    {
        "FROM_FIELD_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "email"         : "me@emailaddress.com",
            "name"          : "My From Name"
        }
    }
```

---

####add_account_from_field<a name="add_account_from_field"/>

Assign email address to account. It can be used in newly created campaigns.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"    : "My other other name",
            "email"   : "and.me.again@my-another-emailaddress.com"
        }
    ]
```

_JSON response:_

```json
    {
        "FROM_FIELD_ID" : "abc123",
        "added"         : 1
    }
```

_JSON error messages (if any):_ `Invalid email syntax`.

**Hint**: When you add from field from web interface clicking confirmation link is required. But when you use API then it is confirmed by default and ready to use.

---

####get_account_domains<a name="get_account_domains"/>

Get domains assigned to account using web interface.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "ACCOUNT_DOMAIN_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "domain"        : "emailaddress.com"
        },
        "ACCOUNT_DOMAIN_ID" : {
            "created_on"    : "2009-01-02 00:00:00",
            "domain"        : "otheremailaddress.com"
        }
    }
```

**Warning**: Please note that after you add domain using web interface it takes up to 24h for us to check DNS propagation. So your domain may not be visible in API method output instantly.

---

####get_account_domain<a name="get_account_domain"/>

Get single domain assigned to account using web interface. Comes in handy when you need to check which domain has campaign assigned.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "account_domain"    : "ACCOUNT_DOMAIN_ID"
        }
    ]
```

_JSON response:_

```json
    {
        "ACCOUNT_DOMAIN_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "domain"        : "emailaddress.com"
        }
    }
 ```

 ---
 
####get_account_customs<a name="get_account_customs"/>
 
 Get defined customs for contacts on account.
 

 _JSON request:_

 ```json
     [
         "API_KEY"
     ]
 ```

 _JSON response:_

 ```json
    {
        "CUSTOM_ID": {
            "name"          : "age",
            "content_type"  : "number",
            "input_type"    : "text",
            "is_hidden"     : "no"
        },
        "CUSTOM_ID": {
            "name"          : "comment",
            "content_type"  : "string",
            "input_type"    : "textarea",
            "is_hidden"     : "yes"
        },
        "CUSTOM_ID": {
            "name"          : "likes_food",
            "content_type"  : "string",
            "input_type"    : "multi_select",
            "is_hidden"     : "no",
            "contents"      : [ "meat", "fruits" ]
        }
    }
```    

---

####add_account_custom<a name="add_account_custom"/>

Add contact custom definition to account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"          : "value",
            "content_type"  : "value",
            "input_type"    : "value",
            "is_hidden"     : "value",
            "contents"      : [ "value", "value", "value" ]
        }
    ]
```

Conditions:

* `name` (mandatory) – Name of custom, must be composed using lowercase letters, digits and underscores only.
* `content_type` (mandatory) – Allowed values are `string`, `number`, `date` and `phone`.
* `input_type` (mandatory) – Allowed values are `text`, `textarea`, `radio`, `checkbox`, `single_select` and `multi_select`.
* `is_hidden` (mandatory) – Allowed values are `true` and `false`. Hidden custom is not visible for contact on his unsubscribe / manage details page.
* `contents` (mandatory if `input_type` is one of `radio`, `checkbox`, `single_select`, `multi_select`) - Provide list of contents to be available for selection from those input types.

_JSON response:_

```json
    {
        "CUSTOM_ID"    : "abc123",
        "added"        : 1
    }
```
 
_JSON error messages (if any):_ `Name already taken`, `Missing contents`.

***Hint:*** You don't have to use this method unless you need to enforce specific content type or define multi content custom. Single content string customs are created on the fly when using methods such as [add_contact](#add_contact) or [set_contact_customs](#set_contact_customs).

---

####set_account_custom_contents<a name="set_account_custom_contents"/>

Modify list of custom contents available for selection.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "custom"    : "CUSTOM_ID",
            "contents"  : [ "value", "value", "value" ]
        }
    ]
```

Conditions:

* `custom` (mandatory) – `CUSTOM_ID` obtained from [get_account_customs](#get_account_customs) or [add_account_custom](#add_account_custom).
* `contents` (mandatory) - New list of contents to be available for selection in `radio`, `checkbox`, `single_select`, `multi_select` input types.

_JSON response:_

```json
    {
        "added"     : 2,
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing custom`, `Not selectable input type`.

***Warning:*** All custom contents that were present in already existing custom but are not present in new `contents` list will be removed from contacts that have them assigned! This action is ***NOT reversible***!

---

####delete_account_custom<a name="delete_account_custom"/>

Remove custom from account and all contacts.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "custom"    : "CUSTOM_ID"
        }
    ]
```

Conditions:

* `custom` (mandatory) – `CUSTOM_ID` obtained from [get_account_customs](#get_account_customs) or [add_account_custom](#add_account_custom).

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing custom`.

***Warning:*** This action is ***NOT reversible***!

---

####get_campaigns<a name="get_campaigns"/>

Get list of campaigns in account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"  : { "OPERATOR" : "value" }
        }
    ]
```

Conditions:

* `name` (optional) – Use [text operators](#operators) to narrow down search results to specific campaign names.

_JSON response:_

```json
    {
        "CAMPAIGN_ID" : {
            "name"              : "my_campaign_1",
            "description"       : "My campaign",
            "optin"             : "single",
            "from_name"         : "My From Name",
            "from_email"        : "me@emailaddress.com",
            "reply_to_email"    : "replies@emailaddress.com",
            "created_on"        : "2010-01-01 00:00:00"
        },
        "CAMPAIGN_ID" : {
            "name"              : "my_campaign_2",
            "description"       : null,
            "optin"             : "double",
            "from_name"         : "My From Name",
            "from_email"        : "me@emailaddress.com",
            "reply_to_email"    : "replies@emailaddress.com",
            "created_on"        : "2010-01-01 00:00:00"
        }
    }
```

**Hint**: There can be only one campaign of a given name, so if you need it’s `CAMPAIGN_ID` perform search like this:

```json
    [
        "API_KEY",
        {
            "name"  : { "EQUALS" : "your_campaign_1" }
        }
    ]
```

and the only one key from response is `CAMPAIGN_ID`.

---

####get_campaign<a name="get_campaign"/>

Get single campaign using `CAMPAIGN_ID`.
Useful for checking which campaign the contact or message belongs to.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"  : "CAMPAIGN_ID"
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID`.

_JSON response:_

```json
    {
        "CAMPAIGN_ID" : {
            "name"              : "my_campaign_1",
            "description"       : "My campaign",
            "optin"             : "single",
            "from_name"         : "My From Name",
            "from_email"        : "me@emailaddress.com",
            "reply_to_email"    : "replies@emailaddress.com",
            "created_on"        : "2010-01-01 00:00:00"
        }
    }
```

---

####add_campaign<a name="add_campaign"/>

Add campaign to account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"                  : "my_new_campaign",
            "description"           : "My new campaign",
            "from_field"            : "FROM_FIELD_ID",
            "reply_to_field"        : "FROM_FIELD_ID",
            "confirmation_subject"  : "CONFIRMATION_SUBJECT_ID",
            "confirmation_body"     : "CONFIRMATION_BODY_ID",
            "language_code"         : "PL"
        }
    ]
```

Conditions:

* `name` (mandatory) – Value of name must be composed of lowercase letters, digits and underscores only.
* `description` (optional) – User friendly name of campaign.
* `from_field` (mandatory) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It is default From header (name and email) in messages sent from this campaign.
* `reply_to_field` (mandatory) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields).
* `confirmation_subject` (mandatory) – `CONFIRMATION_SUBJECT_ID` obtained from [get_confirmation_subjects](#get_confirmation_subjects). Used in confirmation messages sent from this campaign if double-optin is set for given subscription method.
* `confirmation_body` (mandatory) – `CONFIRMATION_BODY_ID` obtained from [get_confirmation_bodies](#get_confirmation_bodies). Used in confirmation messages sent from this campaign if double-optin is set for given subscription method.
* `language_code` (optional) – Language of subscription reminder and change details / unsubscribe footer. List of available ISO 639-1 (2-letter) codes is available [here](http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes). If we don’t have version in requested language then English version will be used.

_JSON response:_

```json
    {
        "CAMPAIGN_ID"   : "abc123",
        "added"         : 1
    }
```

_JSON error messages (if any):_ `Total limit of campaigns exceeded`, `Invalid email syntax`, `Name already taken`, `Missing From field`, `Missing Reply-To field`, `Missing confirmation subject`, `Missing confirmation body`, `Invalid language code`.

**Warning**: Account has limit of 500 campaigns.

**Warning**: Campaign added through API will be visible on web interface after next log-in.

---

####get_campaign_domain<a name="get_campaign_domain"/>

Check if any account domain is assigned to campaign. Assigned domain will be used in links in messages sent from this campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign" : "CAMPAIGN_ID"
        }
    ]
```

Conditions:

* `campaigns` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).

_JSON response:_

```json
    {
        "ACCOUNT_DOMAIN_ID" : {
            "created_on"    : "2009-01-01 00:00:00",
            "domain"        : "emailaddress.com"
        }
    }
```

Empty result means that no domain is assigned.

_JSON error messages (if any):_ `Missing campaign`.

---

####set_campaign_domain<a name="set_campaign_domain"/>

Assign account domain to campaign. Assigned domain will be used in links in messages sent from this campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"          : "CAMPAIGN_ID",
            "account_domain"    : "ACCOUNT_DOMAIN_ID"
        }
    ]
```

Conditions:

* `campaigns` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).
* `account_domain` (mandatory) – `ACCOUNT_DOMAIN_ID` obtained from [get_account_domains](#get_account_domains).

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Missing account domain`.

**Warning**: Any messages sent from now on from this campaign will use this domain in links, even if message was scheduled before domain assignment.

---

####delete_campaign_domain<a name="delete_campaign_domain"/>

Detach account domain from campaign. Unassigned domain will no longer be used in links in messages sent from this campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"  : "CAMPAIGN_ID"
        }
    ]
```

Conditions:

* `campaigns` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`.

**Hint**: This does not delete domain from account. Domain is still visible in [get_account](#get_account) domain result.

**Warning**: Any messages sent from now on from this campaign will not use this domain in links, even if message was scheduled before domain assignment was deleted.

---

####get_campaign_postal_address<a name="get_campaign_postal_address"/>

Get postal address and postal design (formatting) in campaign. Postal address is attached to every message sent from campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"  : "CAMPAIGN_ID"
        }
    ]
```

Conditions:

* `campaigns` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).

_JSON response:_

```json
    {
        "name"      : "My name",
        "address"   : "My address",
        "city"      : "My city",
        "state"     : "My state",
        "zip"       : "My zip",
        "country"   : "My country",
        "design"    : "[[name]], [[address]], [[city]], [[state]] [[zip]], [[country]]"
    }
```

Empty result means that no postal address is assigned to domain. In such cases postal address from account is used. Fields `name` and `state` are optional and may not appear in response (and in design).

_JSON error messages (if any):_ `Missing campaign`.

---

####set_campaign_postal_address<a name="set_campaign_postal_address"/>

Set postal address and postal design (formatting) in campaign. Postal address is attached to every message sent from campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign" : "CAMPAIGN_ID",
            "name" : "My name",
            "address" : "My address",
            "city" : "My city",
            "state" : "My state",
            "zip" : "My zip",
            "country" : "My country",
            "design" : "[[name]], [[address]], [[city]], [[state]] [[zip]], [[country]]"
        }
    ]
```

Conditions:

* `campaigns` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).
* `name` (optional) - Name of you or your company.
* `address` (mandatory) – Street and number.
* `city` (mandatory) – City.
* `state` (optional) - State or region.
* `zip` (mandatory) – Zip / postal code.
* `country` (mandatory) – Country. Name must be compatible with the one on web interface.
* `design` (mandatory) – How your postal address will be formatted. Fields above marked as mandatory must also be present in design! Do not insert HTML tags here, this will be converted in HTML part of messages automatically.

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Token missing in design`.

---

####get_messages<a name="get_messages"/>

Get messages in account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions },
            "type"          : "value",
            "subject"       : { "OPERATOR" : "value" }
            "send_on"       : { "OPERATOR" : "value" }
            "created_on"    : { "OPERATOR" : "value" }
        }
    ]
```

Conditions:

* `campaigns` / `get_campaigns` (optional) – Search only in given campaigns. Uses OR logic. If those params are not given search, is performed in all campaigns in the account. Check [IDs in conditions](#ids) for detailed explanation.
* `type` (optional) – Use "newsletter", "autoresponder" or "draft" to narrow down search results to specific message types. If not given newsletters and autoresponders are returned in the result.
* `subject` (optional) – Use [text operators](#operators) to narrow down search results to specific message subjects.
* `send_on` (optional) – Use [time operators](#operators) to narrow down search results to specific sending date. Multiple operators are allowed and logic AND is used so date range can also be expressed. Works only for newsletters because other message types do not have fixed sending point in time. If message was sent with Time Travel then it may appear in search results for two different days as sending period equals 24 hours.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific message creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.


_JSON response:_

```json
    {
        "MESSAGE_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "type"          : "autoresponder",
            "subject"       : "My autoresponder",
            "day_of_cycle"  : 8,
            "flags"         : ["clicktrack", "openrate"],
            "created_on"    : "2010-01-01 00:00:00"
        },
        "MESSAGE_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "type"          : "newsletter",
            "subject"       : "My newsletter",
            "send_on"       : "2010-01-01 00:00:00",
            "created_on"    : "2010-01-01 00:00:00"
        }
    }
```

Array `flags` may be present with following items:<a name="message_flags"/>

* `clicktrack` – Clicks on links in message are counted.  Note that for the link to be click-tracked it must be also wrapped in [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC#clicktracked_links) `{{LINK}}` tag. This behaves differently than web interface, where (for simplicity) all links are click-tracked when click-track checkbox is set.
* `subscription_reminder` – Short disclaimer is added to the message content to make sure that subscriber know why they are receiving the messages.
* `openrate` – Opened messages are counted (only if html content is present).
* `google_analytics` – Google Analytics code is appended to every link in message.

**Hint**: All merge-words in subject are returned as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax.

**Hint**: If type is autoresponder then `day_of_cycle` is returned and if type is newsletter then `send_on` is returned. Those fields are not present in drafts.

**Hint**: If you need plain and HTML contents of your message use [get_message_contents](#get_message_contents) method.

---

####get_message<a name="get_message"/>

Get single message using `MESSAGE_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"   : "MESSAGE_ID"
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID`.

_JSON response:_

```json
    {
        "MESSAGE_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "type"          : "autoresponder",
            "subject"       : "My autoresponder",
            "day_of_cycle"  : 8,
            "flags"         : ["clicktrack", "openrate"],
            "created_on"    : "2010-01-01 00:00:00"
        }
    }
```

---

####get_message_contents<a name="get_message_contents"/>

Get message contents (parts).

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"   : "MESSAGE_ID"
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID`.

_JSON response:_

```json
    {
        "plain" : "Hello there",
        "html"  : "<h1>Hello</h1>there"
    }
```

_JSON error messages (if any):_ `Missing message`.

**Hint**: Result may be empty (draft messages from web interface do not require content) or contain only `plain`, only `html` or both.

**Hint**: All merge-words in contents are [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax.

---

####get_message_stats<a name="get_message_stats"/>

Get message statistics.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"   : "MESSAGE_ID",
			"grouping" : "daily"
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID`.
* `grouping` (optional) – Determines period of time by which stats are aggregated. Allowed values are: `hourly` (result keys in "YYYY-MM-DD HH" format), `daily` (result keys in "YYYY-MM-DD" format), `monthly` (result keys in "YYYY-MM" format) and `yearly` (result keys in "YYYY" format). Default is `daily`.

_JSON response:_

```json
    {
        "2010-01-01" : {
            "sent"      : 1024,
            "opened"    : 512,
            "clicked"   : 128,
			"forwarded"	: 2,
            "bounces_user_unknown"  : 8,
            "bounces_mailbox_full"  : 2,
            "bounces_block_content" : 0,
            "bounces_block_timeout" : 0,
            "bounces_block_other"   : 1,
            "bounces_other_soft"    : 16,
            "bounces_other_hard"    : 2,
            "complaints_handled"    : 1,
            "complaints_unhandled"  : 0
        },
        "2010-01-02" : {
            "sent"      : 0,
            "opened"    : 64,
            "clicked"   : 16,
			"forwarded"	: 0,
            "bounces_user_unknown"  : 0,
            "bounces_mailbox_full"  : 1,
            "bounces_block_content" : 0,
            "bounces_block_timeout" : 0,
            "bounces_block_other"   : 0,
            "bounces_other_soft"    : 2,
            "bounces_other_hard"    : 0,
            "complaints_handled"    : 1,
            "complaints_unhandled"  : 0
        }
    }
```

_JSON error messages (if any):_ `Missing message`.

**Hint**: It is normal to have stats for given date with `sent` equals 0 and other values positive because opens, clicks, bounces and complaints take place also during a few days after message was sent.

**Warning**: Graduation may not be continuous. Given time period is present in result only if it has at least one positive value in it.

---

####send_newsletter<a name="send_newsletter"/>

Queue a newsletter to be sent.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"          : "CAMPAIGN_ID",
            "from_field"        : "FROM_FIELD_ID",
            "reply_to_field"    : "FROM_FIELD_ID",
            "subject"   : "My newsletter",
            "contents"  : {
                "plain" : "Hello there",
                "html"  : "<h1>Hello</h1>there"
            },
            "attachments" : [
                {
                "data" : "WmHFvMOzxYLEhyBnxJnFm2zEhSBqYcW6xYQu==",
                "name" : "order.txt",
                "mime" : "application/txt"
                },
                {
                "data" : "QWxhLGtvdA==",
                "name" : "people.csv",
                "mime" : "application/csv"
                },
            ],
            "flags" : [ "clicktrack", "openrate" ],
            "contacts"          : [ "CONTACT_ID", "CONTACT_ID" ],
            "get_contacts"      : { get_contacts conditions },
            "segments"          : [ "SEGMENT_ID", "SEGMENT_ID" ],
            "get_segments"      : { get_segments conditions },
            "suppressions"      : [ "SUPPRESSION_ID", "SUPPRESSION_ID" ],
            "get_suppressions"  : { get_suppressions conditions }

        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns). Newsletter will be saved in this campaign. Note that it is not the same as selecting contacts – check `contacts` / `get_contacts` params for that.
* `from_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents From header (name and email) in message and will be taken from campaign if not given.
* `reply_to_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents Reply-To header (email) in message and will not be present if not given.
* `subject` (mandatory) – Subject value. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 512 characters.
* `contents` (mandatory) – Allowed keys are `plain` and `html`, at least one is mandatory. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 524288 characters each.
* `attachments` (optional) - Files that will be attached to message. Field `data` must be encoded using [Base64](http://en.wikipedia.org/wiki/Base64) algorithm. Filed `name` represents name of file. Field `mime` represents [media type](http://en.wikipedia.org/wiki/Internet_media_type) of file.
* `flags` (optional) – Enables extra functionality for a message, see [message_flags](#message_flags) for available values.
* `contacts` / `get_contacts` or `segments` / `get_segments` - Recipients that should receive a newsletter obtained from [get_contacts](#get_contacts) or [get_segments](#get_segments). Only one type of selection can be used at a time. See [IDs in conditions](#ids) for detailed explanation.
* `suppressions` / `get_suppressions` (optional) – Suppressions to use with that message. Any contact email address that matches any of the masks in those suppressions will be skipped when sending. See [IDs in conditions](#ids) for detailed explanation.

_JSON response:_

```json
    {
        "MESSAGE_ID"    : "abc123",
        "queued"        : 1,
        "contacts"      : 1024,
        "segments"      : 4
    }
```

Where:

* `contacts` - Represents the number of unique email addresses that are set to receive this newsletter chosen by `contacts` or `get_contacts` conditions. Presence of contact on suppressions / blacklists does not affect this counter. 
* `segments` - Represents the number of unique segments that are set to receive this newsletter chosen by `segments` or `get_segments` conditions. Segments are evaluated when message is sent, therefore amount of contacts is unknown during API call.

Those counters are mutually exclusive - only one is present depending on how recipients were chosen.

_JSON error messages (if any):_ `Missing campaign`, `Missing From field`, `Missing Reply-To field`, `Missing contents`, `Missing recipients`, `Cannot mix contact and segment recipients`, `Dynamic Content syntax error`, `Daily limit of newsletters exceeded`.

**Hint**: You don’t have to worry about duplicates when sending to multiple campaigns. If the same email exists in my_campaign_1 and my_campaign_2 campaigns then newsletter will be sent only once to this address (chosen randomly).

**Warning**: You can send 256 newsletters daily. Common mistake is to call API in the following manner:

```json
    [
        "API_KEY",
        {
            "subject" : "Hi John",
            ...
            "contacts" : [ "CONTACT_ID" ],
        }
    ]
```

and again…

```json
    [
        "API_KEY",
        {
            "subject" : "Hi Jessica",
            ...
            "contacts" : [ "CONTACT_ID" ],
        }
    ]
```

and again…

```json
    [
        "API_KEY",
        {
            "subject" : "Hi Bruce",
            ...
            "contacts" : [ "CONTACT_ID" ],
        }
    ]
```

If you iterate through your contact list and personalize every call then you’re doing it wrong! It can be compared to sending a group of passengers from city A to B, but every passenger travels in his own train.
Correct way of sending personalized content is to use [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC#campaign_or_contact_or_message_info) syntax for campaign or contact or message info and then send newsletter once to a group of people:

```json
    [
        "API_KEY",
        {
            "subject" : "Hi {{CONTACT \"subscriber_first_name\"}}",
            ...
            "contacts" : [ "CONTACT_ID", "CONTACT_ID", "CONTACT_ID" ],
        }
    ]
```

**Hint**: You can check status of newsletter using [get_newsletter_statuses](#get_newsletter_statuses) method.

---

####add_autoresponder<a name="add_autoresponder"/>

Add a autoresponder to the campaign at the specific day of cycle.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"          : "CAMPAIGN_ID",
            "from_field"        : "FROM_FIELD_ID",
            "reply_to_field"    : "FROM_FIELD_ID",
            "subject"   : "My autoresponder",
            "contents"  : {
                "plain" : "Hello there",
                "html"  : "<h1>Hello</h1>there"
            },
            "attachments" : [
                {
                "data" : "WmHFvMOzxYLEhyBnxJnFm2zEhSBqYcW6xYQu==",
                "name" : "order.txt",
                "mime" : "application/txt"
                },
                {
                "data" : "QWxhLGtvdA==",
                "name" : "people.csv",
                "mime" : "application/csv"
                },
            ],
            "flags" : [ "clicktrack", "openrate" ],
            "day_of_cycle" : 32
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns). Follow-up will be saved in this campaign.
* `from_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents From header (name and email) in message and will be taken from campaign if not given.
* `reply_to_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents Reply-To header (email) in message and will not be present if not given.
* `subject` (mandatory) – Subject value. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 512 characters.
* `contents` (mandatory) – Allowed keys are `plain` and `html`, at least one is mandatory. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 524288 characters each.
* `attachments` (optional) - Files that will be attached to message. Field `data` must be encoded using [Base64](http://en.wikipedia.org/wiki/Base64) algorithm. Filed `name` represents name of file. Field `mime` represents [media type](http://en.wikipedia.org/wiki/Internet_media_type) of file.
* `flags` (optional) – Enables extra functionality for a message, see [message_flags](#message_flags) for available values.
* `day_of_cycle` – Number of days between the day when a contact subscribed to a campaign and the day when the autoresponder is sent. Must be in the range of 0..10000.

_JSON response:_

```json
    {
        "MESSAGE_ID"    : "abc123",
        "added"         : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Missing From field`, `Missing Reply-To field`, `Missing contents`, `Dynamic Content syntax error`.

---

####add_draft<a name="add_draft"/>

Add a draft of given message type to the campaign. Useful for autosave features.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"          : "CAMPAIGN_ID",
            "from_field"        : "FROM_FIELD_ID",
            "reply_to_field"    : "FROM_FIELD_ID",
            "subject"   : "My draft",
            "contents"  : {
                "plain" : "Hello there",
                "html"  : "<h1>Hello</h1>there"
            },
            "flags" : [ "clicktrack", "openrate" ]
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns). Draft will be saved in this campaign.
* `from_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents From header (name and email) in message and will be taken from campaign if not given.
* `reply_to_field` (optional) – `FROM_FIELD_ID` obtained from [get_account_from_fields](#get_account_from_fields). It represents Reply-To header (email) in message and will not be present if not given.
* `subject` (mandatory) – Subject value. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 512 characters.
* `contents` (mandatory) – Allowed keys are `plain` and `html`, at least one is mandatory. All merge-words should be written as [GetResponse Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) syntax. Maximum length is 524288 characters each.
* `flags` (optional) – Enables extra functionality for a message, see [message_flags](#message_flags) for available values.

_JSON response:_

```json
    {
        "MESSAGE_ID"    : "abc123",
        "added"         : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Missing From field`, `Missing Reply-To field`, `Missing contents`.

**Hint**: Drafts can be obtained by using [get_messages](#get_messages) with `type` param.

---

####delete_newsletter<a name="delete_newsletter"/>

Delete newsletter from campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"   : "MESSAGE_ID"
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID` obtained from [get_messages](#get_messages) or [send_newsletter](#send_newsletter).

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing message`, `Message is not newsletter`, `Message send event already passed`.

**Warning**: You can delete only newsletters that have `send_on` date in future.

---

####delete_autoresponder<a name="delete_autoresponder"/>

Delete autoresponder from campaign.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"   : "MESSAGE_ID"
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID` obtained from [get_messages](#get_messages) or [add_autoresponder](#add_autoresponder).

_JSON response:_

```json
    {
        "deleted" : 1
    }
```

_JSON error messages (if any):_ `Missing message`, `Message is not autoresponder`.

---

####set_autoresponder_cycle<a name="set_autoresponder_cycle"/>

Set day of cycle of existing autoresponder.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "message"       : "MESSAGE_ID",
            "day_of_cycle"  : 64
        }
    ]
```

Conditions:

* `message` (mandatory) – `MESSAGE_ID` obtained [get_messages](#get_messages) or [add_autoresponder](#add_autoresponder).
* `day_of_cycle` – Number of days between the day when a contact subscribed to a campaign and the day when the autoresponder is sent. Must be in the range of 0..10000.

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Missing message`, `Message is not autoresponder`.

---

####get_messages_amount_per_account<a name="get_messages_amount_per_account"/>

Get total messages amount on your account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "type"  : "value"
        }
    ]
```

Conditions:

* `type` (optional) – Use newsletter, autoresponder or draft to narrow down count results to specific message types.

_JSON response:_

```json
    {
        "ACCOUNT_ID" : 16
    }
```

---

####get_messages_amount_per_campaign<a name="get_messages_amount_per_campaign"/>

Get total messages amount in every campaign on your account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "type"  : "value"
        }
    ]
```

Conditions:

* `type` (optional) – Use newsletter, autoresponder or draft to narrow down count results to specific message types.

_JSON response:_

```json
    {
        "CAMPAIGN_ID"   : 8,
        "CAMPAIGN_ID"   : 4,
        "CAMPAIGN_ID"   : 2,
        "CAMPAIGN_ID"   : 1,
        "CAMPAIGN_ID"   : 0,
        "CAMPAIGN_ID"   : 1
    }
```

---

####get_newsletter_statuses<a name="get_newsletter_statuses"/>

Get statuses of newsletter messages.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "scheduled"     : [ "MESSAGE_ID", "MESSAGE_ID", "MESSAGE_ID" ],
        "in_progress"   : [ "MESSAGE_ID" ],
        "delivered"     : [ "MESSAGE_ID", "MESSAGE_ID", "MESSAGE_ID", "MESSAGE_ID" ]
    }
```

---

####get_contacts<a name="get_contacts"/>

Get list of contacts from the account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions },
            "name"          : { "OPERATOR" : "value" },
            "email"         : { "OPERATOR" : "value" },
            "created_on"    : {
                "OPERATOR"  : "value",
                "OPERATOR"  : "value"
            },
            "changed_on"    : {
                "OPERATOR"  : "value",
                "OPERATOR"  : "value"
            },
            "origin"        : "value",
            "cycle_day"     : { "OPERATOR" : "value" },
            "customs"   : [
                {
                    "name"      : "name_1_value",
                    "content"   : { "OPERATOR" : "content_1_value" }
                },
                {
                    "name"      : "name_2_value",
                    "content"   : { "OPERATOR" : "content_2_value" }
                }
            ],
            "geoip" : {
                "latitude"      : { "OPERATOR" : "latitude_value" },
                "longitude"     : { "OPERATOR" : "longitude_value" },
                "country"       : { "OPERATOR" : "country_value" },
                "country_code"  : { "OPERATOR" : "country_code_value" },
                "city"          : { "OPERATOR" : "city_value" }
            },
            "clicks"        : [ "LINK_ID", "LINK_ID" ],
            "get_clicks"    : { get_links conditions },
            "opens"         : [ "MESSAGE_ID", "MESSAGE_ID" ],
            "get_opens"     : { get_messages conditions },
			"goals"         : [ "GOAL_ID", "GOAL_ID" ],
            "get_goals"     : { get_goals conditions },
            "segmentation"  : {
                "split" : split_value,
                "pack"  : pack_value
            }
        }
    ]
```

Conditions:

* `campaigns` / `get_campaigns` (optional) – Search only in given campaigns. Uses OR logic. If those params are not given, search is performed in all campaigns within the account. Check [IDs in conditions](#ids) for detailed explanation.
* `name` (optional) – Use [text operators](#operators) to narrow down search results to specific contact names.
* `email` (optional) – Use [text operators](#operators) to narrow down search results to specific contact emails.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific contact creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.
* `changed_on` (optional) – Use [time operators](#operators) to narrow down search results to specific contact modification date. Multiple operators are allowed and logic AND is used so date range can also be expressed.
* `origin` (optional) – Narrow down search results by contacts’ origin (subscription method). Allowed values are `import`, `email`, `www`, `panel`, `leads`, `sale`, `api`, `forward`, `survey`, `iphone`, `copy`.
* `cycle_day` (optional) – Use [numeric operators](#operators) to narrow down search results to specific  days of the autoresponder cycles assigned to the contacts. To find contacts that are on day 2 you have to use `{ "EQUALS" : 2 }`. To find inactive contacts pass `{ "EQUALS" : null }` condition. Note that the fact that contact is on day 2 does not mean he received all autoresponder messages for day 2, there are factors such as excluded of days of week that may cause message to be delayed beyond its `cycle_day`.
* `customs` (optional) – Use [text operators](#operators) to narrow down search results to contacts having specific customs. Uses AND logic. Note that if you need OR logic you can use MATCHES operator and use alternative in regular expression. Contacts that don’t have a custom of given name are not returned in results. If custom is multi-value then “any” junction is used: condition is true if any custom value tests true according to the operator used.
* `geo` (optional) – Use operators to narrow down search results to specific contact geo location. Precisely [text operators](#operators) are allowed for country, country_code, city, [numeric operators](#operators) are allowed for latitude and longitude (values are decimal numbers, like -54.5). Uses AND logic. Contacts that don’t have a geo location data are not returned in results.
* `clicks` / `get_clicks` (optional) – Use to narrow down search results to the contacts that clicked specific links. Uses AND logic. See [IDs in conditions](#ids) for detailed explanation.
* `opens` / `get_opens` (optional) – Use to narrow down search results to contacts that opened specific messages. Uses AND logic. See [IDs in conditions](#ids) for detailed explanation.
* `goals` / `get_goals` (optional) – Use to narrow down search results to contacts that reached specific goals. Uses AND logic. See [IDs in conditions](#ids) for detailed explanation.
* `segmentation` (optional) – Allows to fetch big results in smaller packs. Split value defines the number of packs to which contacts will be split. Group defines which pack will be returned in results. For example to get all results in 10 packs call [get_contacts](#get_contacts) 10 times. Set split to 10 and increase pack from 1 to 10.

_JSON response:_

```json
    {
        "CONTACT_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "name"          : "My Contact Name",
            "email"         : "my_contact_1@emailaddress.com",
            "origin"        : "www",
            "ip"            : "1.1.1.1",
            "cycle_day"     : 32,
            "changed_on"    : "2011-11-11 00:00:00",
            "created_on"    : "2010-01-01 00:00:00"
        },
        "CONTACT_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "name"          : "My Contact Name",
            "email"         : "my_contact_2@emailaddress.com",
            "origin"        : "api",
            "ip"            : "1.1.1.1",
            "cycle_day"     : null,
            "changed_on"    : null,
            "created_on"    : "2010-01-01 00:00:00"
        }
```

**Warning**: Email is unique within a campaign, but if search is performed on multiple campaigns, one email address may occur multiple times in results.

**Hint**: Segmentation does not work as pager (LIMIT x, OFFSET y behavior in SQL) and packs are “almost equal”. That means if you perform split = 10 on result that has 1000 contacts, you will get packs containing about 100 contacts. But segmentation has two very important properties that LIMIT x, OFFSET y approach doesn’t have:

* Contacts stay in their packs forever. Contact once found in the pack 23/100 will always be located in it no matter if the list was altered. This property can be used for shard-oriented synchronization.
* Packs don’t overlap. The sum of contacts in packs 1/3, 2/3 and 3/3 is the same as in search without segmentation. This property is important if you want to parallelize operation on contacts.

---

####get_contact<a name="get_contact"/>

Get a single contact using `CONTACT_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "CONTACT_ID" : {
            "campaign"      : "CAMPAIGN_ID",
            "name"          : "My Contact Name",
            "email"         : "my_contact_1@emailaddress.com",
            "origin"        : "www",
            "ip"            : "1.1.1.1",
            "cycle_day"     : 32,
            "changed_on"    : null,
            "created_on"    : "2010-01-01 00:00:00"
        }
    }
```

---

####set_contact_name<a name="set_contact_name"/>

Set contact name.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID",
            "name"      : "name_value"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.
* `name` (mandatory) – New value of name.

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Missing contact`.

---

####get_contact_customs<a name="get_contact_customs"/>

Get list of contact customs.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "car"   : "big",
        "bike"  : [ "blue", "white" ]
    }
```

_JSON error messages (if any):_ `Missing contact`.

**Hint**: If custom has more than one value (multi-value) then it will be returned as an sorted array.

---

####set_contact_customs<a name="set_contact_customs"/>

Set contact customs.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID",
            "customs" : [
                {
                    "name"       : "name_1_value",
                    "content"    : "content_1_value"
                },
                {
                    "name"       : "name_2_value",
                    "content"    : "content_2_value"
                }
            ]
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.
* `customs` (mandatory) – List of one or more customs to set. Value of name must be composed of letters, digits and underscores only.

Custom of a given name is:

* removed if content value is null
* updated to new content value if already present
* added if not present

Name is case insensitive.

_JSON response:_

```json
    {
        "updated"   : 2,
        "added"     : 1,
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing contact`.

**Hint**: One custom name may appear multiple times in case of multi-value customs (custom must be defined as multi-value on web interface). In this case new content will be added to existing custom instead of replacing previous one.

**Hint**: If you want to remove every existing content value for multi-value customs then pass null as `content` at the beginning of method call:

```json
    "customs" : [
        {
            "name"       : "name_1_value",
            "content"    : null
        },
        {
            "name"       : "name_1_value",
            "content"    : "content_1_1_value"
        },
        {
            "name"       : "name_1_value",
            "content"    : "content_1_2_value"
        },
    ]
```
    
This will clear all content values of multi-value custom and set two new ones.

**Warning**: Custom will be silently skipped if it’s multi-value and its content is not in set of allowed values declared on web interface.

**Warning**: Custom will be silently skipped if its content does not match type declared on web interface.

---

####get_contact_geoip<a name="get_contact_geoip"/>

Get contact geo location based on IP address from which the subscription was processed.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "latitude"          : "54.35"
        "longitude"         : "18.6667",
        "country"           : "Poland",
        "region"            : "82",
        "city"              : "Gdańsk",
        "country_code"      : "PL",
        "postal_code"       : null,
        "dma_code"          : "0",
        "continent_code"    : "EU",
        "time_zone"         : "Europe/Warsaw"
    }
```

_JSON error messages (if any):_ `Missing contact`.

**Warning**: Geo location data is based on the IP address from which a contact subscribed, so not every contact has it (for example imported contacts do not have this information) and for some ISPs it points to where the gateway of this ISP is.

---

####get_contact_opens<a name="get_contact_opens"/>

List dates when the messages were opened by contact.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "MESSAGE_ID"    : "2010-01-01 00:00:00",
        "MESSAGE_ID"    : "2010-01-02 00:00:00"
    }
```

_JSON error messages (if any):_ `Missing contact`.

Note that if a contact opened the same message multiple times, only the newest date is listed.

**Hint**: If you want to keep opens synchronized with external database, then setting open [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) is much more efficient than querying this method periodically.

---

####get_contact_clicks<a name="get_contact_clicks"/>

List dates when the links in messages were clicked by contact.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "LINK_ID"   : "2010-01-01 00:00:00",
        "LINK_ID"   : "2010-01-02 00:00:00"
    }
```

_JSON error messages (if any):_ `Missing contact`.

Note that if a contact clicked the same link multiple times only newest date is listed.

**Hint**: If you want to keep clicks synchronized with external database, then setting click [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) is much more efficient than querying this method periodically.

---

####get_contact_goals<a name="get_contact_goals"/>

List dates when the goals were reached by contacts.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "GOAL_ID"   : "2010-01-01 00:00:00",
        "GOAL_ID"   : "2010-01-02 00:00:00"
    }
```

_JSON error messages (if any):_ `Missing contact`.

Note that if a contact reached the same goal multiple times only newest date is listed.

**Hint**: If you want to keep goals synchronized with external database, then setting goal [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) is much more efficient than querying this method periodically.

---

####get_contact_surveys<a name="get_contact_surveys"/>

List survey results filled by contacts.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact" : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
	{
		"SURVEY_ID" :	{
			"QUESTION_ID"	: "Too high!",
			"QUESTION_ID"	: [ "OPTION_ID", "OPTION_ID" ],
        	"created_on"	: "2012-11-06 23:33:11"
    	},
		"SURVEY_ID" :	{
			"QUESTION_ID"	: [ "OPTION_ID" ],
        	"created_on"	: "2012-11-11 12:54:01"
    	}
	}
```

_JSON error messages (if any):_ `Missing contact`.

Questions that do not have predetermined answers (text fields) have response returned as string while questions with predetermined answers (single/multi selects) have array of selected options returned.

Meaning of every `QUESTION_ID` and `OPTION_ID` can be found by calling [get_survey](#get_survey) method with given `SURVEY_ID`.

**Hint**: If you want to keep surveys synchronized with external database, then setting survey [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) is much more efficient than querying this method periodically.

---

####set_contact_cycle<a name="set_contact_cycle"/>

Place a contact on a desired day of the autoresponder cycle or deactivate a contact.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID",
            "cycle_day" : 4
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.
* `cycle_day` (mandatory) – New value of cycle day, must be in the range of 0..10000. If cycle_day is null it deactivates contact.

_JSON response:_

```json
    {
        "updated"   : 1
    }
```

_JSON error messages (if any):_ `Missing contact`.

---

####add_contact<a name="add_contact"/>

Add contact to the list.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"  : "CAMPAIGN_ID",
            "action"    : "action_value",
            "name"      : "name_value",
            "email"     : "email_value",
            "cycle_day" : cycle_day_value,
            "ip"        : "ip_value",
            "customs"   : [
                {
                    "name"      : "name_1_value",
                    "content"   : "content_1_value"
                },
                {
                    "name"      : "name_2_value",
                    "content"   : "content_2_value"
                }
            ]
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID` obtained from [get_campaigns](#get_campaigns).
* `action` (optional) – Allowed modes are `standard`, `insert`, `update`. If standard mode is chosen then a new contact will be added if not already present in a given campaign otherwise existing contact will be updated including name change and customs list merge. If insert mode is chosen then a contact will be added if it doesn’t exist in a given campaign but no updates will be performed otherwise. If update is chosen then a contact will be updated if it exists in a given campaign but no inserts will be performed otherwise. Default is standard.
* `name` (optional) – Name value.
* `email` (mandatory) – Email value.
* `cycle_day` (optional) – Insert contact on a given day at the autoresponder cycle. Value of 0 means the beginning of the cycle. Lack of this param means that a contact will not be inserted into cycle.
* `ip` (optional) – Contact’s IP address used for geo location. Must be given in dotted decimal format.
* `customs` (optional) – List of contact customs. In case of contact update new customs will be inserted and the existing ones will be updated with the new values. Customs not provided on this list will not be removed. Custom name must be composed using lowercase letters, digits and underscores only.

_JSON response:_

```json
    {
        "queued" : 1
    }
```

_JSON error messages (if any):_ `Invalid email syntax`, `Missing campaign`, `Contact already queued for target campaign`.

**Warning**: Adding contact is not an instant action. It will appear on your list after validation or after validation and confirmation (in case of double-optin procedure). You can set subscribe [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) to be notified about successful adding.

**Hint**: It is legal to add a contact already existing in the campaign (check action param for more details) but it is illegal to have the same email added to queue twice.

**Warning**: Optin setting is locked to double optin by default - confirmation email will be sent to newly added contacts. If you want to add contacts already confirmed on your side please contact us using [this form](http://www.getresponse.com/feedback.html?devzone=yes) and provide us with your campaign name and the description of your business model. We will set single optin for this campaign after short verification.

---

####move_contact<a name="move_contact"/>

Move contact from one campaign to another.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID",
            "campaign"  : "CAMPAIGN_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.
* `campaign` (mandatory) – `CAMPAIGN_ID` to which contact should be moved to.

_JSON response:_

```json
    {
        "updated"   : 1
    }
```

_JSON error messages (if any):_ `Missing contact`, `Missing campaign`, `Contact already exists in target campaign` (also thrown if source and target campaigns are the same).

---

####delete_contact<a name="delete_contact"/>

Delete contact.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "contact"   : "CONTACT_ID"
        }
    ]
```

Conditions:

* `contact` (mandatory) – `CONTACT_ID`.

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

**Warning**: This operation cannot be undone.

---

####get_contacts_deleted<a name="get_contacts_deleted"/>

Get deleted contacts.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions },
            "messages"      : [ "MESSAGE_ID", "MESSAGE_ID" ],
            "get_messages"  : { get_messages conditions },
            "email"         : { "OPERATOR" : "value" },
            "reason"        : "value",
            "created_on"    : {
                "OPERATOR"  : "value",
                "OPERATOR"  : "value"
            },
            "deleted_on"   : {
                "OPERATOR" : "value",
                "OPERATOR" : "value"
            }
        }
    ]
```

Conditions:

* `campaigns` / `get_campaigns` (optional) – Search only in given campaigns. Uses OR logic. If those params are not given search is performed in all campaigns on the account. Check [IDs in conditions](#ids) for detailed explanation.
* `messages` / `get_messages` (optional) – Search only contacts removed from given messages, this info is known for example if contact clicked unsubscribe link. Uses OR logic. Check [IDs in conditions](#ids) for detailed explanation.
* `email` (optional) – Use [text operators](#operators) to narrow down search results to specific contact emails.
* `reason` (optional) – Narrow down search results only to contacts removed due to specific reason, allowed values are: `unsubscribe`, `user`, `support`, `automation` (or more specific `automation::remove-on-remove`, `automation::remove-on-sale`, `automation::remove-on-subscribe`), `complaint`, `blacklisted`, `api`, `bounce` (or more specific `bounce::mailbox_full`, `bounce::other_hard`, `bounce::other_soft`, `bounce::user_unknown`), `other`.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific contact creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.
* `deleted_on` (optional) – Use [time operators](#operators) to narrow down search results to specific contact deletion date. Multiple operators are allowed and logic AND is used so date range can also be expressed.

_JSON response:_

```json
    {
        'CONTACT_ID' : {
            "campaign"      : "CAMPAIGN_ID",
            "name"          : "My Contact Name",
            "email"         : "my_contact_1@emailaddress.com"
            "origin"        : "www",
            "ip"            : "1.1.1.1",
            "cycle_day"     : 32,
            "reason"        : "bounce",
            "created_on"    : "2010-01-01 00:00:00",
            "deleted_on"    : "2010-01-02 00:00:00",
        },
        'CONTACT_ID' : {
            "campaign"      : "CAMPAIGN_ID",
            "message"       : "MESSAGE_ID",
            "name"          : "My Contact Name",
            "email"         : "my_contact_2@emailaddress.com"
            "origin"        : "api",
            "ip"            : "1.1.1.1",
            "cycle_day"     : null,
            "reason"        : "other",
            "created_on"    : "2010-01-01 00:00:00",
            "deleted_on"    : "2010-01-01 00:00:00",
        }
    }
```

**Warning**: If a contact was added and removed multiple times from the same campaign it is presented only once in results with newest deleted_on date.

**Warning**: Reasons in result are always general even if you requested specific one.

**Warning**: Unsubscribe link allows contact to unsubscribe from multiple campaigns, even if message was not sent from those campaigns or to contact in those campaigns.

**Hint**: If you want to keep unsubscribes synchronized with external database, then setting unsubscribe [callback](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) is much more efficient than querying this method periodically.

---

####get_contacts_subscription_stats<a name="get_contacts_subscription_stats"/>

Get contacts subscription stats aggregated by time period, campaign and contact’s origin.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions },
            "created_on"    : {
                "OPERATOR" : "value",
                "OPERATOR" : "value"
            },
            "grouping"      : "monthly"
        }
    ]
```

Conditions:

* `campaigns` / `get_campaigns` (optional) – Get statistics only for given campaigns. Uses OR logic. If those params are not given statistics are returned from all campaigns on the account. Check [IDs in conditions](#ids) for detailed explanation.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific contact creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.
* `grouping` (optional) – Determines period of time by which stats are aggregated. Allowed values are: `hourly` (result keys in "YYYY-MM-DD HH" format), `daily` (result keys in "YYYY-MM-DD" format), `monthly` (result keys in "YYYY-MM" format) and `yearly` (result keys in "YYYY" format). Default is `daily`.

_JSON response:_

```json
    {
        "2010-01-01" : {
            "CAMPAIGN_ID"   : {
                "iphone"    : 0,
                "www"       : 32,
                "sale"      : 64,
                "leads"     : 2,
                "forward"   : 0,
                "panel"     : 4,
                "api"       : 128,
                "import"    : 0,
                "email"     : 16,
                "survey"    : 1,
                "copy"      : 0
            },
            "CAMPAIGN_ID"   : {
                "iphone"    : 8,
                "www"       : 0,
                "sale"      : 0,
                "leads"     : 64,
                "forward"   : 0,
                "panel"     : 0,
                "api"       : 512,
                "import"    : 16,
                "email"     : 0,
                "survey"    : 0,
                "copy"      : 1
            }
        },
        "2010-01-02"    : {
            "CAMPAIGN_ID"   : {
                "iphone"    : 0,
                "www"       : 64,
                "sale"      : 128,
                "leads"     : 8,
                "forward"   : 1,
                "panel"     : 8,
                "api"       : 1024,
                "import"    : 0,
                "email"     : 2,
                "survey"    : 8,
                "copy"      : 0
            },
            "CAMPAIGN_ID"   : {
                "iphone"    : 0,
                "www"       : 0,
                "sale"      : 0,
                "leads"     : 128,
                "forward"   : 0,
                "panel"     : 0,
                "api"       : 2048,
                "import"    : 0,
                "email"     : 0,
                "survey"    : 0,
                "copy"      : 0
            }
        }
    }
```

**Warning**: Graduation may not be continuous. Given time period is present in result only if it has at least one positive value in it.

---

####get_contacts_amount_per_account<a name="get_contacts_amount_per_account"/>

Get total contacts amount on your account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "ACCOUNT_ID"    : 128
    }
```

---

####get_contacts_amount_per_campaign<a name="get_contacts_amount_per_campaign"/>

Get total contacts amount in every campaign on your account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "CAMPAIGN_ID"   : 64,
        "CAMPAIGN_ID"   : 32,
        "CAMPAIGN_ID"   : 0,
        "CAMPAIGN_ID"   : 16,
        "CAMPAIGN_ID"   : 16,
        "CAMPAIGN_ID"   : 0,
        "CAMPAIGN_ID"   : 0
    }
```

---

####get_contacts_distinct_amount<a name="get_contacts_distinct_amount"/>

Get amount of unique email addresses of your contacts.

This method is useful for pre-estimating amount of recipients in [send_newsletter](#send_newsletter) method.
Also can help in tracking redundancy when combined with [get_contacts_amount_per_campaign](#get_contacts_amount_per_campaign) method.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions }
        }
    ]
```

Conditions:

* `campaigns` / `get_campaigns` (optional) – Count distinct emails only in given campaigns. Uses OR logic. If those params are not given statistics are returned from all campaigns on the account. Check [IDs in conditions](#ids) for detailed explanation.

_JSON response:_

```json
    64
```

---

####get_segments<a name="get_segments"/>

Get contact segments saved on web interface to use in [send_newsletter](#send_newsletter).

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"  : { "OPERATOR" : "value" }
        }
    ]
```

Conditions:

* `name` (optional) – Use [text operators](#operators) to narrow down search results to specific names.

_JSON response:_

```json
    {
        "SEGMENT_ID" : {
            "name"          : "Females",
            "created_on"    : "2012-04-12 00:00:00"
        },
        "SEGMENT_ID" : {
            "name"          : "Kids",
            "created_on"    : "2012-04-13 00:00:00"
        }
    }
```

---

####get_links<a name="get_links"/>

Get clicktracked links.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "messages"      : [ "MESSAGE_ID", "MESSAGE_ID" ],
            "get_messages"  : { get_messages conditions },
            "url"           : { "OPERATOR" : "value" }
        }
    ]
```

Conditions:

* `messages` / `get_messages` (optional) – Search only in given messages. Uses OR logic. If those params are not given search is performed in all messages on the account. Check [IDs in conditions](#ids) for detailed explanation.
* `url` (optional) – Use [text operators](#operators) to narrow down search results to specific URL addresses.

_JSON response:_

```json
    {
        "LINK_ID"   : {
            "message"   : "MESSAGE_ID",
            "name"      : "My Home Page",
            "url"       : "http://myhomepage.com",
            "clicks"    : 32
        },
        "LINK_ID"   : {
            "message"   : "MESSAGE_ID",
            "name"      : "My product 1",
            "url"       : "http://myhomepage.com?product=1",
            "clicks"    : 16
        }
    }
```

**Warning**: Links will not be visible if the message that contains them did not have click-tracking enabled at the time of sending.

**Hint**: If a message was edited all links are returned in results, not only the current ones.

---

####get_link<a name="get_link"/>

Get single click-tracked link using LINK_ID.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "link"  : "LINK_ID"
        }
    ]
```

_JSON response:_

```json
    {
        "LINK_ID"   : {
            "message"   : "MESSAGE_ID",
            "name"      : "My Home Page",
            "url"       : "http://myhomepage.com",
            "clicks"    : 32
        }
    }
```

---

####get_goals<a name="get_goals"/>

Get goals.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "profile"	: { "OPERATOR" : "value" },
            "domain"	: { "OPERATOR" : "value" },
            "name"		: { "OPERATOR" : "value" },
            "url"		: { "OPERATOR" : "value" }
        }
    ]
```

Conditions:

* `profile` (optional) – Use [text operators](#operators) to narrow down search results to specific profile name.
* `domain` (optional) – Use [text operators](#operators) to narrow down search results to specific goal domain.
* `name` (optional) – Use [text operators](#operators) to narrow down search results to specific goal name.
* `url` (optional) – Use [text operators](#operators) to narrow down search results to specific goal URL.

_JSON response:_

```json
    {
        "GOAL_ID"   : {
            "profile"	: "Goals on my page",
			"domain"	: "myhomepage.com",
			"name"		: "My blog",
            "url"       : "http://myhomepage.com/blog"
        },
        "GOAL_ID"   : {
            "profile"	: "Goals on my page",
			"domain"	: "myhomepage.com",
			"name"		: "My shop",
            "url"       : "http://myhomepage.com/shop"
        }
    }
```

---

####get_goal<a name="get_goal"/>

Get single goal using GOAL_ID.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "link"  : "GOAL_ID"
        }
    ]
```

_JSON response:_

```json
    {
        "GOAL_ID"   : {
            "profile"	: "Goals on my page",
			"domain"	: "myhomepage.com",
			"name"		: "My blog",
            "url"       : "http://myhomepage.com/blog"
        },
        "GOAL_ID"   : {
            "profile"	: "Goals on my page",
			"domain"	: "myhomepage.com",
			"name"		: "My shop",
            "url"       : "http://myhomepage.com/shop"
        }
    }
```

---

####get_surveys<a name="get_surveys"/>

Get surveys.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "status" 	: "value",
            "name"		: { "OPERATOR" : "value" },
        }
    ]
```

Conditions:

* `status` (optional) – Allowed values are `unpublished`, `published` and `closed`.
* `name` (optional) – Use [text operators](#operators) to narrow down search results to specific survey name.

_JSON response:_

```json
    {
        "SURVEY_ID": {
            "name"			: "My survey 1",
			"title"			: "Tell me something about yourself.",
            "description"	: "Trying to engage my potential customers",
            "questions"	: {
	            "QUESTION_ID"	: {
					"name"	: "What do you think about gas prices?",
					"note"	: null
    			},
                "QUESTION_ID"	: {
					"name"	: "What car do you own?",
					"note"	: "You may select multiple brands",
                    "options"	: {
                        "OPTION_ID"	: {	"name": "Dodge" },
                        "OPTION_ID"	: {	"name": "Ford" },
                        "OPTION_ID"	: {	"name": "Uaz" },
                        "OPTION_ID"	: {	"name": "other" }
                    }
                }
            },
            "status" 		: "closed",
            "created_on"	: "2012-01-01 00:00:00"
        },
		"SURVEY_ID": {
            "name"			: "My survey 2",
			"title"			: null,
            "description"	: null,
            "questions"	: {
	            "QUESTION_ID"	: {
					"name"	: "Truth or dare?",
					"note"	: null,
					"options"	: {
                        "OPTION_ID"	: {	"name": "Truth" },
                        "OPTION_ID"	: {	"name": "Dare" }
                    }
    			}
			},
			"status" 		: "published",
	        "created_on"	: "2012-02-02 00:00:00"
		}
    }
```

---

####get_survey<a name="get_survey"/>

Get single survey using SURVEY_ID.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "survey"	: "SURVEY_ID"
        }
    ]
```

_JSON response:_

```json
    {
        "SURVEY_ID": {
            "name"			: "My survey 1",
			"title"			: "Tell me something about yourself.",
            "description"	: "Trying to engage my potential customers",
            "questions"	: {
	            "QUESTION_ID"	: {
					"name"	: "What do you think about gas prices?",
					"note"	: null
    			},
                "QUESTION_ID"	: {
					"name"	: "What car do you own?",
					"note"	: "You may select multiple brands",
                    "options"	: {
                        "OPTION_ID"	: {	"name": "Dodge" },
                        "OPTION_ID"	: {	"name": "Ford" },
                        "OPTION_ID"	: {	"name": "Uaz" },
                        "OPTION_ID"	: {	"name": "other" }
                    }
                }
            },
            "status"		: "closed",
            "created_on"	: "2012-01-01 00:00:00"
        }
	}
```

---

####get_survey_stats<a name="get_survey_stats"/>

Get message statistics with summarized amount of every choices.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "survey"	: "SURVEY_ID",
			"anonymous"	: true
        }
    ]
```

Conditions:

* `survey` (mandatory) – `SURVEY_ID`, may be obtained from [get_surveys](#get_surveys).
* `anonymous` (optional) – Allowed values are `true` and `false`. Survey may be filled by contact (for example when it was sent by email and `CONTACT_ID` is known) or by anonymous visitor (for example when survey is linked on www page). When this param is skipped all responses are counted without distinguishing who filled the survey.

_JSON response:_

```json
	{
    	"QUESTION_ID" 	: 2,
		"QUESTION_ID"	: {
        	"OPTION_ID" : 128,
        	"OPTION_ID" : 2,
        	"OPTION_ID" : 0,
        	"OPTION_ID" : 4
		}
	}
```

_JSON error messages (if any):_ `Missing survey`.

Questions that do not have predetermined answers (text fields) have total amount of responses returned while questions with predetermined answers (single/multi selects) have amount of responses for every option returned.

Meaning of every `QUESTION_ID` and `OPTION_ID` can be found by calling [get_surveys](#get_surveys) method.

---

####get_account_blacklist<a name="get_account_blacklist"/>

Get blacklist masks on account level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "mask"          : { "OPERATOR" : "value" },
            "created_on"    : {
                "OPERATOR"  : "value",
                "OPERATOR"  : "value"
            }
        }
    ]
```

Conditions:

* `mask` (optional) – Use [text operators](#operators) to narrow down search results to specific masks.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific mask creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.


_JSON response:_

```json
    {
        "my_contact_1@emailaddress.com" : "2010-01-01 00:00:00",
        "my_contact_2@emailaddress.com" : "2010-01-01 00:00:00"
    }
```

Format of mask can be:<a name="mask_format"/>

* whole email  – xxx@yyy.zz
* local part of email- xxx@
* host part of email – @yyy.zz
* MD5 hash of email – d6dba89e8479a7049d2d7b2e5b6528ec
* ISP name – #yahoo (note the # on the beginning)
* IP address – 1.2.33.44 (IPv4 format only)

**Warning**: According to [this FAQ](http://www.espcoalition.org/MD5_Suppression_list_encryption_FAQ.pdf) MD5 hash should be generated from lowercased email, check “Why do I have to normalize email addresses prior to encrypting?” section.

---

####add_account_blacklist<a name="add_account_blacklist"/>

Adds blacklist mask on account level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "mask"  : "mask_value"
        }
    ]
```

Conditions:

* `mask` (mandatory) – Mask to blacklist, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "added" : 1
    }
```

_JSON error messages (if any):_ `Cannot set mask` (some masks are forbidden to manage), `Invalid mask syntax`.

---

####delete_account_blacklist<a name="delete_account_blacklist"/>

Delete blacklist mask on account level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "mask"  : "mask_value"
        }
    ]
```

Conditions:

* `mask` (mandatory) – Mask to remove from blacklist, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Cannot set mask` (some masks are forbidden to manage), `Invalid mask syntax`, `Missing mask`.

---

####get_campaign_blacklist<a name="get_campaign_blacklist"/>

Get blacklist masks on campaign level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign" : "CAMPAIGN_ID",
            "mask"          : { "OPERATOR" : "value" },
            "created_on"    : {
                "OPERATOR"  : "value",
                "OPERATOR"  : "value"
            }
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID`.
* `mask` (optional) – Use [text operators](#operators) to narrow down search results to specific masks.
* `created_on` (optional) – Use [time operators](#operators) to narrow down search results to specific mask creation date. Multiple operators are allowed and logic AND is used so date range can also be expressed.

_JSON response:_

```json
    {
        "my_contact_1@emailaddress.com" : "2010-01-01 00:00:00",
        "my_contact_2@emailaddress.com" : "2010-01-01 00:00:00"
    }
```

**Hint**: Check [blacklist masks](#mask_format) for available formats.

---

####add_campaign_blacklist<a name="add_campaign_blacklist"/>

Adds blacklist mask on campaign level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign" : "CAMPAIGN_ID",
            "mask" : "mask_value"
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID`.
* `mask` (mandatory) – Mask to blacklist, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "added" : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Cannot set mask` (some masks are forbidden to manage), `Invalid mask syntax`.

---

####delete_campaign_blacklist<a name="delete_campaign_blacklist"/>

Delete blacklist mask on campaign level.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "campaign"  : "CAMPAIGN_ID",
            "mask"      : "mask_value"
        }
    ]
```

Conditions:

* `campaign` (mandatory) – `CAMPAIGN_ID`.
* `mask` (mandatory) – Mask to remove from blacklist, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing campaign`, `Cannot set mask` (some masks are forbidden to manage), `Invalid mask syntax`, `Missing mask`.

---

####get_suppressions<a name="get_suppressions"/>

Get list of defined suppression lists on your account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "SUPPRESSION_ID"    : {
            "name"          : "Rude people",
            "created_on"    : "2010-01-01 00:00:00",
        },
        "SUPPRESSION_ID"    : {
            "name"          : "Mean people",
            "created_on"    : "2010-01-01 00:00:00",
        }
    }
```

---

####get_suppression<a name="get_suppression"/>

Get single suppression using `SUPPRESSION_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "suppression"   : "SUPPRESSION_ID"
        }
    ]
```

Conditions:

* `suppression` (mandatory) – `SUPPRESSION_ID`.

_JSON response:_

```json
    {
        "SUPPRESSION_ID"    : {
            "name"          : "Rude people",
            "created_on"    : "2010-01-01 00:00:00",
        }
    }
```

---

####add_suppression<a name="add_suppression"/>

Add suppression list to your account.

This method registers named container for masks, which can be added using add_suppression_skiplist.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "name"  : "Weird people"
        }
    ]
```

Conditions:

* `name` (mandatory) – Name of your suppression list, must be unique within account.

_JSON response:_

```json
    {
        "SUPPRESSION_ID"    : abc123",
        "added"             : 1
    }
```

_JSON error messages (if any):_ `Name already used`.

---

####delete_suppression<a name="delete_suppression"/>

Delete suppression list from your account.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "suppression" : "SUPPRESSION_ID"
        }
    ]
```

Conditions:

* `suppression` (mandatory) – `SUPPRESSION_ID`.

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing suppression`.

**Warning**: Every mask defined within this suppression will be lost.

**Warning**: Every queued message that uses this suppression will be send as planned, but contact emails will not be filtered using masks from this suppression.

---

####get_suppression_skiplist<a name="get_suppression_skiplist"/>

Skiplist is a set of masks for suppression. If contact’s email address matches any of those masks in message that uses this suppression then it will be skipped.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "suppression"   : "SUPPRESSION_ID"
        }
    ]
```

Conditions:

* `suppression` (mandatory) – `SUPPRESSION_ID` obtained from [get_suppressions](#get_suppressions).

_JSON response:_

```json
    {
        "by.whole@email.com"                : "2010-01-01 00:00:00",
        "by.local@"                         : "2010-01-01 00:00:00",
        "@by.domain"                        : "2010-01-01 00:00:00",
        "990420c537fd46a6c2af5b00ff94cf51"  : "2010-01-01 00:00:00",
        "#by_isp"                           : "2010-01-01 00:00:00"
    }
```

_JSON error messages (if any):_ `Missing suppression`.

**Hint**: Check suppression masks for available formats.

---

####add_suppression_skiplist<a name="add_suppression_skiplist"/>

Add mask to a set of masks for suppression.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "suppression"   : "SUPPRESSION_ID",
            "mask"          : "by.whole@email.com"
        }
    ]
```

Conditions:

* `suppression` (mandatory) – `SUPPRESSION_ID` obtained from [get_suppressions](#get_suppressions).
* `mask` (mandatory) – Mask to skip, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "added" : 1
    }
```

_JSON error messages (if any):_ `Missing suppression`, `Invalid mask syntax`.

---

####delete_suppression_skiplist<a name="delete_suppression_skiplist"/>

Delete mask from a set of masks for suppression.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "suppression"   : "SUPPRESSION_ID",
            "mask"          : "by.whole@email.com"
        }
    ]
```

Conditions:

* `suppression` (mandatory) – `SUPPRESSION_ID` obtained from [get_suppressions](#get_suppressions).
* `mask` (mandatory) – Mask to delete, check [available formats](#mask_format).

_JSON response:_

```json
    {
        "deleted"   : 1
    }
```

_JSON error messages (if any):_ `Missing suppression`, `Invalid mask syntax`, `Missing mask`.

**Warning**: If any queued message uses this suppression then it will be send as planned, but contact emails will not be filtered using this masks from this suppression.

---

####get_confirmation_subjects<a name="get_confirmation_subjects"/>

Get list of available subjects for confirmation messages. They can be used in campaign settings.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "language_code" : { "EQUALS" : "EN" },
        }
    ]
```

Conditions:

* `language_code` (optional) – Use [text operators](#operators) to narrow down search results to specific languages. List of available ISO 639-1 (2-letter) codes is available [here](http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes).

_JSON response:_

```json
    {
        "CONFIRMATION_SUBJECT_ID"   : {
            "content"       : "Please confirm subscription",
            "language_code" : "EN"
        },
        "CONFIRMATION_SUBJECT_ID"   : {
            "content"       : "Proszę potwierdź subskrypcję",
            "language_code" : "PL"
        }
    }
```

---

####get_confirmation_subject<a name="get_confirmation_subject"/>

Get single subject for confirmation message using `CONFIRMATION_SUBJECT_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "confirmation_subject"  : "CONFIRMATION_SUBJECT_ID",
        }
    ]
```

Conditions:

* `confirmation_subject` (mandatory) – `CONFIRMATION_SUBJECT_ID`.

_JSON response:_

```json
    {
        "CONFIRMATION_SUBJECT_ID"   : {
            "content"       : "Please confirm subscription",
            "language_code" : "EN"
        }
    }
```

---

####get_confirmation_bodies<a name="get_confirmation_bodies"/>

Get list of available bodies for confirmation messages. They can be used in campaign settings.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "language_code" : { "EQUALS" : "EN" },
        }
    ]
```

Conditions:

* `language_code` (optional) – Use [text operators](#operators) to narrow down search results to specific languages. List of available ISO 639-1 (2-letter) codes is available [here](http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes).

_JSON response:_

```json
    {
        "CONFIRMATION_BODY_ID"  : {
            "plain"         : "Please click to confirm ...",
            "html"          : "<p>Please click to confirm ...",
            "language_code" : "EN"
        },
        "CONFIRMATION_BODY_ID"  : {
            "plain"         : "Prosze kliknij aby potwierdzić ...",
            "html"          : "<p>Proszę kliknij aby potwierdzić ...",
            "language_code" : "PL"
        }
    }
```

---

####get_confirmation_body<a name="get_confirmation_body"/>

Get single body for confirmation message using `CONFIRMATION_BODY_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "confirmation_body" : "CONFIRMATION_BODY_ID",
        }
    ]
```

Conditions:

* `confirmation_body` (mandatory) – `CONFIRMATION_BODY_ID`.

_JSON response:_

```json
    {
        "CONFIRMATION_BODY_ID"  : {
            "plain"         : "Please click to confirm ...",
            "html"          : "<p>Please click to confirm ...",
            "language_code" : "EN"
        }
    }
```

---

####get_account_callbacks<a name="get_account_callbacks"/>

Get [callbacks](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) configuration for account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "uri" 		: "http://example.com/callback",
		"actions" 	: [ "subscribe", "open", "click" ]
    }
```

---

####set_account_callbacks<a name="set_account_callbacks"/>

Set [callbacks](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) configuration for account.

_JSON request:_

```json
    [
        "API_KEY",
		{
			"uri" 		: "http://example.com/callback",
			"actions" 	: [ "subscribe", "open", "click", "goal", "survey", "unsubscribe" ]
		}
    ]
```

Conditions:

* `uri` (mandatory) – Location of callback listener.
* `actions` (mandatory) – List of actions that will be reported to callback listener. Allowed values are `subscribe`, `open`, `click`, `goal`, `survey`, `unsubscribe` with at least one value required.

_JSON response:_

```json
    {
        "updated" : 1
    }
```

_JSON error messages (if any):_ `Invalid URI`.

---

####delete_account_callbacks<a name="delete_account_callbacks"/>

Delete [callbacks](https://github.com/GetResponse/DevZone/tree/master/Callback/README.md) configuration for account.

_JSON request:_

```json
    [
        "API_KEY"
    ]
```

_JSON response:_

```json
    {
        "deleted" : 1
    }
```

---

####add_account<a name="add_account"/>

Add new account to server.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "password"      : "12#$qwER",
            "first_name"    : "Andrzej",
            "last_name"     : "Cholewiusz",
            "email"         : "andrzej.cholewiusz@implix.com",
            "company_name"  : "Implix",
            "phone"         : "+48587321918",
            "country_code"  : "PL",
            "country"       : "Poland",
            "city"          : "Gdańsk",
            "state"         : "pomorskie",
            "street"        : "Żabianska",
            "zip_code"      : "24-213",
            "campaign"  : {
                "name"                  : "trains",
                "description"           : "Fani pociągów.",
                "confirmation_subject"  : "CONFIRMATION_SUBJECT_ID",
                "confirmation_body"     : "CONFIRMATION_BODY_ID",
                "language_code"         : "PL",
                "optin"                 : "single",
            }
        }
    ]
```

Conditions:

* `password` (mandatory) – Must be at least 8 characters including lowercase letter, uppercase letter, digit or special character. Note for polish users: this conforms [GIODO increased and high security levels](https://edugiodo.giodo.gov.pl/file.php/1/INF1/INF_R02_01_02.html).
* `first_name` (mandatory) – At least one character.
* `last_name` (mandatory) – At least one character.
* `email` (mandatory) – Valid email address.
* `company_name` (optional) – At least one character.
* `phone` (mandatory) – At least one character. No format is forced.
* `country_code` (mandatory) – Two letter country code as described in [ISO 3166-1](http://en.wikipedia.org/wiki/ISO_3166-1).
* `country` (mandatory) – Name of country as described in [ISO 3166-1](http://en.wikipedia.org/wiki/ISO_3166-1). Must match `country_code`.
* `city` (mandatory) – At least one character.
* `state` (optional) – At least one character.
* `street` (mandatory) – At least one character.
* `zip_code` (mandatory) – At least one character. No format is forced.
* `campaign` (mandatory) – Every account created must also have default campaign. Conditions are explained on [add_campaign](#add_campaign) method with few differences:
* `campaign` `from_field` / `reply_to_field` – Are not passed because default campaign inherits account email address.
* `campaign` `confirmation_subject` (mandatory) – `CONFIRMATION_SUBJECT_ID` obtained from [get_confirmation_subjects](#get_confirmation_subjects). Used in confirmation messages sent from this campaign.
* `campaign` `confirmation_body` (mandatory) – `CONFIRMATION_BODY_ID` obtained from [get_confirmation_bodies](#get_confirmation_bodies). Used in confirmation messages sent from this campaign.
* `campaign` `optin` (optional) – Allows to set campaign optin mode to `single` (unconfirmed) or `double` (confirmed). Note that `campaign` `confirmation_subject` / `confirmation_body` are mandatory even if optin is `single`.

_JSON response:_

```json
    {
        "ACCOUNT_ID"    : "abc123",
        "added"         : 1
    }
```

Every account added through API has its own API KEY generated by default.

_JSON error messages (if any):_ `Owner privilege missing`, `Invalid email syntax`, `Email already taken`, `Password too weak`, `Invalid country`, `Invalid email syntax`, `Name already taken` (for campaign).

---

####get_accounts<a name="get_accounts"/>

List all accounts on server.

_JSON request:_

```json
    [
        "API_KEY",
    ]
```
    
_JSON response:_

```json
    {
        "ACCOUNT_ID" : {
            "login"         : "andee",
            "status"        : "enabled",
            "first_name"    : "Andrzej",
            "last_name"     : "Cholewiusz",
            "email"         : "andrzej.cholewiusz@implix.com",
            "company_name"  : "Implix",
            "phone"         : "+48587321918",
            "country_code"  : "pl",
            "country"       : "Polska",
            "city"          : "Gdańsk",
            "state"         : "pomorskie",
            "street"        : "Żabianska",
            "zip_code"      : "24-213",
            "created_on"    : "2010-11-01 07:27:43",
            "API_KEY"       : "09fb76c7d2ecc0298855259f1dd224a5",
            "api_status"    : "enabled",
            "blocked_features"  : [ "Multimedia" ],
            "send_limit"    : {
                "allowed"       : 1048576,
                "current"       : 8192,
                "reseted_on"    : "2013-01-01 00:00:00"
            }
        },
        "ACCOUNT_ID" : {
             ... another account data ...
        },
        "ACCOUNT_ID" : {
             ... another account data ...
        }
    }
```

_JSON error messages (if any):_ `Owner privilege missing`.

If account does not have any features blocked then whole "blocked_features" field is not present in result. If account does not have limits for sending emails then whole "send_limit" field is not present in result. Every other field is present in result even if it has null value.

---

####get_account<a name="get_account"/>

Get one account on server using `ACCOUNT_ID`.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "account"   : "ACCOUNT_ID"
        }
    ]
```

_JSON response:_

```json
    {
        "ACCOUNT_ID" : {
            "login"         : "andee",
            "status"        : "enabled",
            "first_name"    : "Andrzej",
            "last_name"     : "Cholewiusz",
            "email"         : "andrzej.cholewiusz@implix.com",
            "company_name"  : "Implix",
            "phone"         : "+48587321918",
            "country_code"  : "pl",
            "country"       : "Polska",
            "city"          : "Gdańsk",
            "state"         : "pomorskie",
            "street"        : "Żabianska",
            "zip_code"      : "24-213",
            "created_on"    : "2010-11-01 07:27:43",
            "API_KEY"       : "09fb76c7d2ecc0298855259f1dd224a5",
            "api_status"    : "enabled",
            "blocked_features"  : [ "Multimedia" ],
            "send_limit"    : {
                "allowed"       : 1048576,
                "used"          : 8192,
                "reseted_on"    : "2013-01-01 00:00:00"
            }
        }
    }
``` 

_JSON error messages (if any):_ `Owner privilege missing`.
    
This method is useful to obtain API KEY of newly created account.

---

####set_account_status<a name="set_account_status"/>

Enable or disable account or its features.

_JSON request:_

```json
    [
        "API_KEY",
        {
            "account"           : "ACCOUNT_ID",
            "status"            : "enabled",
            "block_features"    : [ "CreateCampaign", "Multimedia" ],
            "send_limit"        : 1048576
        }
    ]
```

Conditions:

* `account` (mandatory) – Identifier of account. If identifier is incorrect then Missing account error will be returned.
* `status` (optional) – May be ‘enabled’ or ‘disabled’. If this param is skipped existing status is not modified.
* `block_features` (optional) - Prevent account from accessing specific features. Names of those features can be seen on "Owner settings" -> "Accounts List" -> "edit details" -> "Features blocked" pulldown menu. If this param is skipped existing blocks are not modified. If this param is given previous blocks are removed and new list of blocks is applied. Therefore to remove all blocks (enable all features) empty array should be passed as param value. Current list of blocks can be obtained using [get_account](#get_account) method.
* `send_limit` (optional) - Maximum amount of emails that account can send. If this param is skipped existing limit is not modified. If this param is null then any existing limit is removed. If this param is given limit is set but without modifying existing "used" or "reseted_on" values - which can be checked in [get_account](#get_account) method under "send_limit".

_JSON response:_

```json
    {
        "result" : {
            "updated" : "1"
        }
    }
```

_JSON error messages (if any):_ `Owner privilege missing`, `Missing account`, `Cannot modify owner account` (owner account cannot set status of itself or another owner account), `Invalid feature name`.

**Warning**: Setting `send_limit` with and without removing previous limit behaves differently. Let's say you have account that was created in January.

* in January `send_limit` was set to 1000, in February `send_limit` was set to 2000, in March `send_limit` was set to 3000
* in January `send_limit` was set to 1000, in February previous `send_limit` was removed and new `send_limit` was set to 1000, in March previous `send_limit` was removed and new `send_limit` was set to 1000

In both cases account can send 3000 emails within 3 months. But in first case account can use remaining pool from previous months (for example it can send 100 emails in January, 100 emails in February and 2800 emails in March) while in second case it is not possible.

**Warning:** Never use batch call to reset previous limit and set a new one in one call because - according to the spec - server may process those two requests in any order.

##OPERATORS<a name="operators"/>

There may be several types of operators in method conditions.

Text:

* `EQUALS`, `NOT_EQUALS` – Compare text literally. Case insensitive.
* `CONTAINS`, `NOT_CONTAINS` – Compare text with wild chars. Use % for any string and _ for one character. Case insensitive.

Numeric:

* `LESS`, `LESS_OR_EQUALS`, `EQUALS`, `GREATER_OR_EQUALS`, `GREATER` – Compare numbers. Use . (dot) as decimal part separator if needed.

Time:

* `FROM`, `TO`, `AT` – Compare dates in YYYY-MM-DD format.

**Warning**: Operators must be UPPERCASED.

##IDs in conditions<a name="ids"/>

In many methods IDs may be passed to conditions. Method [get_messages](#get_messages) will be used as an example of how to do it.

```json
    [
        "API_KEY",
        {
            "campaigns"     : [ "CAMPAIGN_ID", "CAMPAIGN_ID" ],
            "get_campaigns" : { get_campaigns conditions },
            ...
        }
    ]
```

Method expects a list of campaigns in conditions to narrow down search results.
Those campaigns may be given as an array of `CAMPAIGN_ID` values obtained from [get_campaigns](#get_campaigns).

```json
    [
        "API_KEY",
        {
            "campaigns" : [ "4b1b5", "b35f2b" ]
        }
    ]
```

It will return messages from those two specific campaigns.
But sometimes it is faster/easier to nest [get_campaigns](#get_campaigns) conditions in [get_messages](#get_messages) method.

```json
    [
        "API_KEY",
        {
            "get_campaigns" : { "CONTAINS" : "my_campaign_%" }
        }
    ]
```

It will return messages from campaigns that have name beginning with my_campaign_. Pretty easy, isn’t it?

**Hint**: Nesting of conditions is also legal. For example [send_newsletter](#send_newsletter) conditions may contain [get_contacts](#get_contacts) conditions which may contain [get_links](#get_links) conditions which may contain [get_messages](#get_messages) conditions which may contain [get_campaigns](#get_campaigns) conditions. Everything in one request!

**Warning**: Do not use `campaigns` when you mean `get_campaigns`. And the other way round. Condition `campaigns` expects array of `CAMPAIGN_ID` values while condition `get_campaigns` expects [get_campaigns](#get_campaigns) method conditions. Same for `messages`/`get_messages`, `contacts`/`get_contacts` and others.

**Warning**: Do not provide text where `ID` is expected. This is incorrect.

```json
    "campaigns" : [ "my_campaign_1" ]
```

##ERRORS

Field `error` format is compatible with [JSON-RPC 2.0 spec](http://www.jsonrpc.org/specification#error_object).

Errors not included in spec:

* Invalid API key.

```json
    {
        "code"      : -1,
        "message"   : "API key verification failed",
    }
```

* Client IP not from trusted network (GetResponse 360 only)

```json
    {
        "code"      : -1,
        "message"   : "API client IP not allowed",
    }
```

* Method error, list of `message` values in this error is provided in method documentation.

```json
    {
        "code"      : -1,
        "message"   : "...",
    }
```


##CHANGELOG<a name="changelog"/>

version 1.31.0, 2013-07-24
* [get_contacts_deleted](#get_contacts_deleted) allows to request more specific reason

version 1.30.0, 2013-06-25

* [add_account](#add_account) no longer requires `login` as unique identifier and to follow changes on web interface `email` must be unique accross accounts
* [add_account](#add_account) has `Login already taken` error replaced by `Email already taken`
* [add_account](#add_account) has `Invalid email syntax` error added

version 1.29.2, 2013-06-24

* [set_account_callbacks](#set_account_callbacks) has "Invalid URI" error added

version 1.29.1, 2013-06-17

* [get_contact_customs](#get_contact_customs), [get_contact_geoip](#get_contact_geoip), [get_contact_opens](#get_contact_opens), [get_contact_clicks](#get_contact_clicks), [get_contact_goals](#get_contact_goals), [get_contact_surveys](#get_contact_surveys) return `Missing contact` error instead of empty result when contact is not present
* [get_message_contents](#get_message_contents) [get_message_stats](#get_message_stats) return `Missing message` error instead of empty result when message is not present
* [get_survey_stats](#get_survey_stats) returns `Missing survey` error instead of empty result when survey is not present

version 1.29.0, 2013-06-17

* [get_account_blacklist](#get_account_blacklist) and [get_campaign_blacklist](#get_campaign_blacklist) allow to narrow down result by "mask" or "created_on" date

version 1.28.0, 2013-05-27

* [get_account_customs](#get_account_customs),[add_account_custom](#add_account_custom), [set_account_custom_contents](#set_account_custom_contents), [delete_account_custom](#delete_account_custom) methods added for custom definitions management

version 1.27.0, 2013-05-16

* [set_account_status](#set_account_status) gained ability to manage send limit, [get_accounts](#get_accounts) / [get_account](#get_account) list it under "send_limit"

version 1.26.0, 2013-05-15

* [get_contacts](#get_contacts) can narrown down result by `changed_on`
* [set_account_status](#set_account_status) gained ability to block specific features, [get_accounts](#get_accounts) / [get_account](#get_account) list them under "blocked_features"

version 1.25.0, 2013-04-25

* [get_contacts](#get_contacts) and [get_contact](#get_contact) return "changed_on" value
* [set_contact_name](#set_contact_name) and [set_contact_customs](#set_contact_customs) update contact "changed_on" value

version 1.24.1, 2013-04-18

* [set_contact_name](#set_contact_name) works if character representation of name is the same but octet representation is different

version 1.24.0, 2013-04-17

* [get_messages](#get_messages) accepts `created_on` and `send_on` params

version 1.23.0, 2013-04-05

* [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up), [set_follow_up_cycle](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#set_follow_up_cycle), [delete_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#delete_follow_up) are handled by [add_autoresponder](#add_autoresponder), [set_autoresponder_cycle](#set_autoresponder_cycle), [delete_autoresponder](#delete_autoresponder) - compatibility mapping will be available till the end of the year
* [add_autoresponder](#add_autoresponder) and [set_autoresponder_cycle](#set_autoresponder_cycle) no longer have `Day of cycle already used` error, it is possible to create many messages for the same day in one campaign.
* [get_messages](#get_messages) and [get_message](#get_message) return "autoresponder" type instead of "follow-up", requesting "follow-up" in `type` param will be possible till the end of the year
* [get_contacts](#get_contacts) and [get_contact](#get_contact) may return `cycle_day` greater than maximum `day_of_cycle` within autoresponder messages - contact is no longer "deactivated" after receiving last autoresponder message

version 1.22.0, 2013-01-04

* [get_contacts_subscription_stats](#get_contacts_subscription_stats) returns copied contacts as separate counter
* copied contacts can be obtained using [get_contacts](#get_contacts) with `origin` param

version 1.21.0, 2012-12-17

* [get_newsletter_statuses](#get_newsletter_statuses) added
* [get_campaigns](#get_campaigns) has `optin` param added in response

version 1.20.0, 2012-12-11

* [get_contacts_distinct_amount](#get_contacts_distinct_amount) added

version 1.19.0, 2012-12-11

* [get_contacts_subscription_stats](#get_contacts_subscription_stats) has `grouping` param added

version 1.18.2, 2012-12-04

* support for gzipped transfer

version 1.18.1, 2012-12-04

* [get_message_stats](#get_message_stats) has `resolution` param renamed to `grouping` to be more intuitive

version 1.18.0, 2012-12-03

* [get_message_stats](#get_message_stats) has `resolution` param added

version 1.18.0, 2012-11-30

* added support for [Batches](http://www.jsonrpc.org/specification#batch) and [Notifications](http://www.jsonrpc.org/specification#notification) in JSON-RPC 2.0

version 1.17.1, 2012-11-30

* added note about dropping JSON-RPC 1.1 draft specification

version 1.17.0, 2012-11-30

* [account_callbacks](#get_account_callbacks) has survey callback added

version 1.16.0, 2012-11-21

* [get_surveys](#get_surveys), [get_survey](#get_survey), [get_survey_stats](#get_survey_stats) and [get_contact_surveys](#get_contact_surveys) for surveys added

version 1.15.0, 2012-11-14

* [get_message_stats](#get_message_stats) has `forwarded` counter added

version 1.14.0, 2012-11-09

* [get_account_callbacks](#get_account_callbacks), [set_account_callbacks](#set_account_callbacks) and [delete_account_callbacks](#delete_account_callbacks)
  for callback management added

version 1.13.0, 2012-11-09

* [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up), [set_follow_up_cycle](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#set_follow_up_cycle) and [set_contact_cycle](#set_contact_cycle)
  have maximum follow-up day increased from 1000 to 10000.

version 1.12.0, 2012-09-17

* added [get_goals](#get_goals), [get_goal](#get_goal) and [get_contact_goals](#get_contact_goals) methods
* [get_contacts](#get_contacts) accepts goals

version 1.11.1, 2012-07-26

* [add_account](#add_account) has strict checking of `country` and `country_code`
* [send_newsletter](#send_newsletter) daily calls limit bumped to 256

version 1.11.0, 2012-07-25

* [add_account](#add_account), [get_accounts](#get_accounts), [get_account](#get_account)
  no longer support `company_country`, `company_city`, `company_street`, `company_state`, `company_zip_code`

version 1.10.0, 2012-07-12

* [send_newsletter](#send_newsletter) and [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up) allow to send attachments

version 1.9.4, 2012-05-15

* [get_message_contents](#get_message_contents) result may be empty
  because new editor on web interface has subject and contents editing on separate steps
  and it is allowed to save draft message with subject only

version 1.9.3, 2012-05-15

* draft is now separate type instead of variant of newsletter/follow-up
* [get_messages](#get_messages) has `draft_mode` param removed,
  drafts can be selected using `type` param and are labeled as 'draft' in result
* [add_draft](#add_draft) has `type` param removed

version 1.9.2, 2012-05-10

* error `Invalid language code` added to [add_campaign](#add_campaign),
  language codes are returned uppercased to be compatible with ISO

version 1.9.1, 2012-04-19

* `last_click_on` and `last_open_on` removed from [get_contacts](#get_contacts) as obsolete,
  sending to segment has better implementation of this logic

version 1.9.0, 2012-04-13

* added [get_segments](#get_segments) method
* [send_newsletter](#send_newsletter) accepts segments, also
  error `Contacts list empty` changed to more generic `Missing recipients`
  and `Cannot mix contact and segment recipients` error added

version 1.8.13, 2012-03-12

* ability to restrict access to trusted networks by IP addresses (GetResponse 360 only)

version 1.8.12, 2012-03-09

* [send_newsletter](#send_newsletter), [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up) and [add_draft](#add_draft) accept `reply_to_field` param

version 1.8.11, 2012-03-08

* [add_account_blacklist](#add_account_blacklist), [add_campaign_blacklist](#add_campaign_blacklist) and [add_suppression_skiplist](#add_suppression_skiplist) support IP address in [mask formats](#mask_format)

version 1.8.10, 2012-01-19

* [get_contact_geoip](#get_contact_geoip) returns `continent_code` and `time_zone`

version 1.8.9, 2011-11-15

* contact `name` is optional in [add_contact](#add_contact)

version 1.8.8, 2011-10-10

* following response fields that were previously JSON-string has now more narrow typing to JSON-number:
  [get_messages](#get_messages) - `day_of_cycle`,
  [get_message](#get_message) - `day_of_cycle`,
  [get_message_stats](#get_message_stats) - `sent`, `opened`, `clicked`, `bounces_*`, `complaints_*`,
  [get_contacts](#get_contacts) - `cycle_day`,
  [get_contact](#get_contact) - `cycle_day`,
  [get_contacts_deleted](#get_contacts_deleted) - `cycle_day`,
  [get_contacts_subscription_stats](#get_contacts_subscription_stats) - `import`, `email`, `www`, `panel`, `leads`, `sale`, `api`, `forward`, `survey`, `iphone`,
  [get_contacts_amount_per_account](#get_contacts_amount_per_account) - calculated amount,
  [get_contacts_amount_per_campaign](#get_contacts_amount_per_campaign) - calculated amounts,
  [get_links](#get_links) - `clicks`,
  [get_link](#get_link) - `clicks`,
* online documentation fixes to respect difference between JSON-string and JSON-number types

version 1.8.7, 2011-10-10

* added [get_messages_amount_per_account](#get_messages_amount_per_account) method
* added [get_messages_amount_per_campaign](#get_messages_amount_per_campaign) method

version 1.8.6, 2011-10-10

* [get_contacts_subscription_stats](#get_contacts_subscription_stats) allows to narrow results by `campaigns` / `get_campaigns` params

version 1.8.5, 2011-09-20

* [get_contacts_deleted](#get_contacts_deleted) can narrow down result to contacts removed from specific messages and from specific reason

version 1.8.4, 2011-08-22

* [send_newsletter](#send_newsletter) and [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up) methods can detect [Dynamic Content](https://github.com/GetResponse/DevZone/tree/master/DC) errors in subject and contents

version 1.8.3, 2011-08-10

* reasons in [get_contacts_deleted](#get_contacts_deleted) method changed

version 1.8.2, 2011-07-05

* added `description` param to [add_campaign](#add_campaign) and [get_campaigns](#get_campaigns) methods

version 1.8.1, 2011-05-18

* added [add_draft](#add_draft) method to manage drafts, [get_messages](#get_messages) method can work in draft mode
* possibility to delete scheduled newsletter using [delete_newsletter](#delete_newsletter)

version 1.8.0, 2011-03-03

* added methods to manage suppression lists: 
  [get_suppressions](#get_suppressions),
  [get_suppression](#get_suppression),
  [add_suppression](#add_suppression),
  [delete_suppression](#delete_suppression),
  [get_suppression_skiplist](#get_suppression_skiplist),
  [add_suppression_skiplist](#add_suppression_skiplist),
  [delete_suppression_skiplist](#delete_suppression_skiplist)
* method [send_newsletter](#send_newsletter) accepts suppression lists

version 1.7.4, 2011-03-02

* blacklist masks accept [local@ and MD5 formats](#mask_formats)

version 1.7.3, 2011-02-22

* possibility to choose From header in [send_newsletter](#send_newsletter) and [add_follow_up](https://github.com/GetResponse/DevZone/blob/d95effbc61dc2b931703af534b46a6cd1221a4c8/API/README.md#add_follow_up) methods

version 1.7.2, 2011-02-11

* [send_newsletter](#send_newsletter) limited to 128 calls daily to prevent client-side customization anti-pattern explained in method description

version 1.7.1, 2011-02-11

* added methods to check amount of contacts:
  [get_contacts_amount_per_account](#get_contacts_amount_per_account),
  [get_contacts_amount_per_campaign](#get_contacts_amount_per_campaign),

version 1.7.0, 2010-11-25

* added domains management methods:
  [get_account_domains](#get_account_domains),
  [get_account_domain](#get_account_domain),
  [get_campaign_domain](#get_campaign_domain),
  [set_campaign_domain](#set_campaign_domain),
  [delete_campaign_domain](#delete_campaign_domain)
* added postal address management methods:
  [get_campaign_postal_address](#get_campaign_postal_address),
  [set_campaign_postal_address](#set_campaign_postal_address)

    