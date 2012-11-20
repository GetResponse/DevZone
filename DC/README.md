#GetResponse Dynamic Content

version 1.6.6, 2012-11-06 [changelog](#changelog)

##GETTING STARTED

Dynamic Content is a message-composing language that enables GetResponse users with ***web developing skills*** to create personalized messages.

####General rules

Dynamic Content is a programing language, and so it requires strict syntax:

There can be only one instruction per tag.
Syntax is case-sensitive, casing is like in the examples.
Back-ticks can be used instead of double quotes when composing HTML messages.

##SUPPORT

The GetResponse Dynamic Content is created and maintained by the *GetResponse DevZone Team*.

If you run into an error or you have difficulties with using the DC please contact us using [this form](http://www.getresponse.com/feedback.html?devzone=yes) and we will provide all the support we can to solve your problems.

##TAGS

* [campaign predefined values](#campaign_predefined_values)
* [campaign or contact or message info](#campaign_or_contact_or_message_info)
* [contact custom fields](#contact_custom_fields)
* [contact geo location](#contact_geo_location)
* [contact subscription date](#contact_subscription_date)
* [clicktracked links](#clicktracked_links)
* [system links](#system_links)
* [social links](#social_links)
* [qrcode links](#qrcode_links)
* [dates](#dates)
* [timers](#timers)
* [randoms](#randoms)
* [currency conversions](#currency_conversions)
* [conditions](#conditions)
* [external content](#external_content)

---

####campaign predefined values

Merge-words that are constant in your campaign.

```
{{PREDEFINED "my_shop_name"}}
```

[Beautifulizers](#beautifulizers) are allowed.

Predefined values can be defined on [GetResponse WWW](https://app.getresponse.com/campaigns_predefined_variables.html). Empty string is inserted into the message on undefined value.

---

####campaign or contact or message info

Basic merge-words providing info about campaign, contact or message. Please note that "contact" is called a "subscriber" in tokens due to backward compatibility.

```
{{CONTACT "subscriber_name"}}
```

Supported tokens:

* `subscriber_name`
* `subscriber_first_name` – Everything up to first space.
* `subscriber_last_name` – Empty if name doesn’t consist of two parts separated by space.
* `subscriber_email`
* `subscriber_ip`
* `subscriber_origin`
* `campaign_name`
* `campaign_description`
* `campaign_from_name`
* `campaign_from_email`
* `campaign_reply_to_email`
* `message_from_name`
* `message_from_email`
* `message_reply_to_email`
* `campaign_id`
* `message_id`
* `subscriber_id`

[Beautifulizers](#beautifulizers) are allowed.

Empty string is inserted into the message on undefined value. This behavior can be changed by providing default value.

```
{{CONTACT "subscriber_name" "My friend"}}
```

**Hint**: Tokens `campaign_id`, `message_id`, and `subscriber_id` return the same `IDs` as in [API](https://github.com/GetResponse/DevZone/tree/master/API). For example you can pass `subscriber_id` token in [link](#clicktracked_links):

```
{{LINK "http://myshop.com/index.html?visitor={{CONTACT "subscriber_id"}}"}}
```

After receiving click on your server use this `ID` to get additional info about visitor from API using [get_contact](https://github.com/GetResponse/DevZone/tree/master/API#get_contact) or [get_contact_customs](https://github.com/GetResponse/DevZone/tree/master/API#get_contact_customs) methods.

---

####contact custom fields

Merge-words for contact custom (additional) fields.

```
{{CUSTOM "car"}}
```

[Beautifulizers](#beautifulizers) are allowed.

Contact custom fields can be defined on [GetResponse WWW](https://app.getresponse.com/custom_fields_list.html) or by [GetResponse API](https://github.com/GetResponse/DevZone/tree/master/API#set_contact_customs). Empty string is inserted into the message on undefined value. This behavior can be changed by providing default value.

```
{{CUSTOM "car" "your car"}}
```

---

####contact geo location

Geo location info based on contact IP (if available).

```
{{GEO "country"}}
```

Supported tokens:

* `latitude`
* `longitude`
* `country_code`
* `country`
* `city`
* `region`
* `postal_code`
* `dma_code`
* `continent_code`
* `time_zone` - Given as name, not as offset. For example "Europe/Warsaw".

[Beautifulizers](#beautifulizers) are allowed.

Empty string is inserted into the message on undefined value. This behavior can be changed by providing default value.

```
{{GEO "city" "Your city"}}
```

---

####contact subscription date

Contact subscription date that can be presented in various formats.

```
{{ADDED_ON "you subscribed on DAY_ORDINATED of MONTH_NAME, it was DAY_NAME"}}
```
(will insert "you subscribed on 11th of July, it was Friday")

Format part can contain special tokens (uppercased!) that will be replaced with date parts.

* `CENTURY` – For example "21".
* `YEAR` – For example "2012".
* `YEAR_SHORT` – For example "10".
* `MONTH` – From "01" to "12".
* `MONTH_NAME` – From "January" to "December".
* `MONTH_NAME_SHORT` – From "Jan" to "Dec".
* `DAY` – From "01" to "31".
* `DAY_NAME` – From "Monday" to "Sunday".
* `DAY_NAME_SHORT` – From "Mon" to "Sun".
* `HOUR` – From "00" to "23".
* `MINUTE` – From "00" to "59".
* `SECOND` – From "00" to "59".

Format can also contain every other character except double quotes and back-tick to format date.

**Hint**: You can get rid of leading zeroes by suffixing numeric token with `_NONZERO`.

* `HOUR_NONZERO` – From "0" to "23".

**Hint**: You can ordinate numeric token value by suffixing it with `_ORDINATED`.

* `DAY_ORDINATED` – From "1st", "2nd", "3rd" to "31st".

Ordinated values are always stripped of leading zeros.

**Warning**: You cannot use `_NONZERO` or `_ORDINATED` with naming tokens like `DAY_NAME`.

---

####clicktracked links

Mark links for click counting tracking and assign optional description for statistics.

```
{{LINK "http://mysite.com"}}
```

For messages composed on [GetResponse WWW](https://app.getresponse.com/choose_html_or_plain.html?msg_type=broadcast) editor all links are marked for tracking automatically and tracking takes place if "Track click-through's of your links" on editor window is chosen.

For messages sent using [GetResponse API](https://github.com/GetResponse/DevZone/tree/master/API#send_newsletter) links must be wrapped in `{{LINK}}` tag on clients side to mark it for tracking. Also

```json
    "flag"  : [ "clicktrack" ]
```

must be passed to method.

**Warning:** Tracking is not the same as wrapping links in HTML tags. To make tracked link clickable in HTML you still need to use anchor.

```html
<a href="{{LINK`http://mysite.com`}}">My site</a>
```

Note that you can use back-ticks instead of double quotes and remove white spaces to avoid trouble with HTML editors.

**Hint**: You can use [campaign predefined values](#campaign_predefined_values), [campaign or contact or message_info](#campaign_or_contact_or_message_info), [contact custom fields](#contact_custom_fields) or [contact geo location](#contact_geo_location) tags inside your URL.

```
{{LINK "http://mysite.com/login?email={{CONTACT "subscriber_email"}}&from_where={{GEO "city"}}"}}
```

Nested tags have following restrictions:

* Can not contain default value - `{{CONTACT "subscriber_name" "Friend"}}` is forbidden.
* Can not use [beautifulizers](#beautifulizers) - `{{CONTACT "uc(subscriber_name)"}}` is forbidden.
* Are encoded in UTF-8 as as URL param, so if `{{CONTACT "subscriber_name"}}` is "Pabian Paweł" it will be inserted into link as "Pabian%20Pawe%C5%82" to make link valid. Please note that "/" character will also be encoded as "%2F" so do not use nested tag as part of your link path.

**Hint**: You can give links a custom description.

```
{{LINK "http://mysite.com" "My site"}}
```

If description is given link is shown under this name instead of URL on performance stats. It makes stats easier to read and allows to do two tricks – splitting and merging.

Splitting allows to show many links with the same URL under different names. It may be useful for statistical purposes like creating a "click heat map" of your message.

```
Hello
{{LINK "http://mysite.com" "My page at the beginning of the message"}}
Some message body
{{LINK "http://mysite.com" "My page at the end of the message"}}
Sincerely
```

Now you know which link was clicked more often because - despite common URL - those links will be shown under separate names on performance stats.

Merging is opposite to splitting – many links with different URLs will be shown under one name on performance stats. This is helpful if you do not need to distinguish them.

```
{{LINK "http://mysite.com/shop/main" "Main shop page"}}
{{LINK "http://myoldsite.com/redirect/shop" "Main shop page"}}
```

---

####system links

Links that allow contact to perform actions.

```
{{LINK "me"}}
```

Supported tokens and message types they can be used in:

* `me` (newsletter, follow-up, rss) – Manage subscription and details. This is the same link as in footer.
* `change_details`, `unsubscribe` - Obsolete, replaced by `me`.
* `view` (newsletter, follow-up, rss) – View message on website. May be useful if contact is using email client that blocks content, for example images.
* `forward` (newsletter, follow-up, rss) – Forward message to a friend.
* `next` (follow-up) – Get next message in follow-up cycle without waiting.
* `play` (newsletter, follow-up, rss) – Play message using Email 2 Speech feature.
* `confirm` (confirmation) - Confirm subscription in double optin mode.

---

####social links

Links that allow contact to share info about message on social media.

```
{{LINK "social_digg"}}
```

Allowed tokens:

* `social_digg`
* `social_facebook`
* `social_googleplus`
* `social_linkedin`
* `social_myspace`
* `social_twitter`

**Warning**: If you want your newsletter to be shared on social media you must select "Publish in RSS" on last step of creating newsletter process in old editor.

---

####qrcode links

Links that allow to place QR Code into your message.

```
{{LINK "qrcode" "1234"}}
```

QR Codes can be defined on [GetResponse WWW](https://app.getresponse.com/manage_multimedia.html?type=qrcodes). Use number from target png file as param.

**Hint**: You can use [campaign predefined values](#campaign_predefined_values), [campaign or contact or message_info](#campaign_or_contact_or_message_info), [contact custom fields](#contact_custom_fields) or [contact geo location](#contact_geo_location) tags inside your QR Code content. This way you can generate different QR codes for every email sent and create promotions based on pool of defined promo codes.

Nested tags have following restrictions:

* Can not contain default value - `{{CONTACT "subscriber_name" "Friend"}}` is forbidden.
* Can not use beautifulizers - `{{CONTACT "uc(subscriber_name)"}}` is forbidden.

---

####dates

Date of email delivery that can be presented in various formats and be time-shifted.

```
{{DATE "this email was sent on DAY_ORDINATED of MONTH_NAME, it was DAY_NAME"}}
```
(will insert "this email was sent on 11th of July, it was Friday")

Format part can contain special tokens (uppercased!) that will be replaced with date parts.

* `CENTURY` – For example "21".
* `YEAR` – For example "2012".
* `YEAR_SHORT` – For example "10".
* `MONTH` – From "01" to "12".
* `MONTH_NAME` – From "January" to "December".
* `MONTH_NAME_SHORT` – From "Jan" to "Dec".
* `DAY` – From "01" to "31".
* `DAY_NAME` – From "Monday" to "Sunday".
* `DAY_NAME_SHORT` – From "Mon" to "Sun".
* `HOUR` – From "00" to "23".
* `MINUTE` – From "00" to "59".
* `SECOND` – From "00" to "59".

Format can also contain every other character except double quotes and back-tick to format date.

**Hint**: You can get rid of leading zeroes by suffixing numeric token with `_NONZERO`.

* `HOUR_NONZERO` – From "0" to "23".

**Hint**: You can ordinate numeric token value by suffixing it with `_ORDINATED`.

* `DAY_ORDINATED` – From "1st", "2nd", "3rd" to "31st".

Ordinated values are always stripped of leading zeros.

**Warning**: You cannot use `_NONZERO` or `_ORDINATED` with naming tokens like `DAY_NAME`.

**Hint**: Date supports time-shift.

```
{{DATE "YEAR-MONTH-DAY" "+10 DAY"}}
```

Offset must be given as "+" or "-" sign, value of time shift and unit of time shift. Allowed units are.

* `YEAR`(`S`) – 365 days.
* `MONTH`(`S`) – 30 days.
* `DAY`(`S`)
* `HOUR`(`S`)

Total amount of time shift cannot exceed +/-16 years. If it does then unmodified date will be inserted into a message.

---

####timers

Countdown to/since given timestamp or contact subscription date.

```
{{TIMER "2012-21-12 00:00:00" "DAYS_UNIT HOURS_UNIT to the end of the world" "World ended DAYS days ago"}}
```
(will insert "291 days 7 hours to the end of the world" at the moment this doc was created and will insert "World ended 32 days ago" when it is 32 days after the timestamp date)

```
{{TIMER "added_on" "" "You signed up DAYS_UNIT ago"}}
```
(will insert "You signed up 35 days ago")

First param is timestamp that can be provided as:

* Date and time - "YYYY-MM-DD HH:MM:SS"
* Date - "YYYY-MM-DD"
* Contact subscription date - "added_on"

Second / third params (both are mandatory, even if empty) are future / past formats. They are displayed if the message was sent before / after given timestamp. Those formats can contain special tokens that will be replaces with amounts of those units.

* `CENTURIES`
* `YEARS` - 365 days.
* `MONTHS` – 30 days.
* `DAYS`
* `HOURS`
* `MINUTES`
* `SECONDS`

**Hint**: If you want unit name along with numeric value suffix token with `_UNIT`, for example:

```
{{TIMER "2010-01-01 00:00:00" "HOURS_UNIT" ""}}
```
(will insert "8 hours")

**Hint**: Tokens are greedy. It means that you do not have to use all of them in formats. Tokens you use can "consume" amount of time in a smart way.

```
{{TIMER "2010-01-01 00:00:00" "HOURS_UNIT" ""}}
```
(will insert "49 hours")

```
{{TIMER "2001-01-01 00:00:00" "DAYS_UNIT and HOURS_UNIT" ""}}
```
(will insert "2 days and 1 hour" because `DAYS` token "consumed" 48 hours)

---

####randoms

Inserts random text from provided list.

```
{{RANDOM "Hi" "Hello" "Hey"}}
```

---

####currency conversions

Convert between price currencies in your email depending on which country is your contact from.

```
{{CURRENCY "1000" "USD"}}
```
(will insert "1000 USD" if contacts country code based on GeoIP is "US" (or not defined) and will insert "3097.20 PLN" if contacts country code is PL)

Source currency must be given as 3-letters code defined in [ISO 4217](http://iso4217.com/).

Target currency is determined by 2-letters `{{CUSTOM "country_code"}}` value or `{{GEO "country_code"}}` if contact custom field value is not present. Conversion rates are taken from [European Central Bank](http://www.ecb.int) when message is sent.

---

####conditions

Allows to display various parts of message depending on [campaign or contact or message_info](#campaign_or_contact_or_message_info), [contact custom fields](#contact_custom_fields), [contact geo location](#contact_geo_location) or [dates](#dates).

```
{{IF "(pet IS_DEFINED)"}}
    {{IF "(pet STRING_EQ 'dog')"}}
        Buy a bone for your dog!
        {{LINK "http://mysite.com/shop/product/bone_for_dog"}}
    {{ELSIF "(pet STRING_EQ 'cat')"}}
        Buy a mouse toy for your cat!
        {{LINK "http://mysite.com/shop/product/toy_mouse_for_cat"}}
    {{ELSE}}
        You may find something for your pet in my store!
    {{ENDIF}}
{{ELSE}}
    You don't have a pet yet. Buy one!
{{ENDIF}}

{{IF "((city STRING_EQI 'Gdańsk') LOGIC_OR (city STRING_EQI 'Gdynia') LOGIC_OR (city STRING_EQI 'Sopot'))"}}
    You can visit our store located in 3City in person!
{{ELSE}}
    Go to our online page: {{LINK "http://mysite.com/shop"}}
{{ENDIF}}
{{IF "(day_name STRING_NEQ 'Sunday')"}}
    We're open today.
{{ELSE}}
    Please visit us from Monday to Saturday
{{ENDIF}}
```

Order of tags:

* `{{IF "..."}}` – Mandatory beginning of conditional statement.
* `{{ELSIF "..."}}` – Optional block, multiple allowed.
* `{{ELSE}}` – Optional default block, one allowed.
* `{{ENDIF}}` – Mandatory closing of conditional statement.

Building conditions step by step:

* Take left operand, this can be name of [contact custom field](#contact_custom_fields) or token from [campaign or contact or message_info](#campaign_or_contact_or_message_info), [contact geo location](#contact_geo_location), [dates](#dates).

```
DAY
```

* Lowercase this operand.

```
day
```

* Take right operand which is constant value you like left operand to be compared against.

```
8
```

* Quote this operand in single quotes, this is mandatory even if operand is numeric.

```
'8'
```

* Join operands with operator.

```
day NUMBER_GT '8'
```

* Wrap this pair in round brackets to get conditional statement.

```
(day NUMBER_GT '8')
```

* (optional) If you need more complex logic you can join conditional statements with logic operator. Remember about top round brackets to create conditional statement.

```
((day NUMBER_GT '8') LOGIC_AND (day NUMBER_LT '16'))
```

* Use conditional statement in tag param.

```
{{IF "((day NUMBER_GT '8') LOGIC_AND (day NUMBER_LT '16'))"}}
    You will see this if message was sent between 8th and 16th day of month.
{{ENDIF}}
```

Operators:

* `NUMBER_LT` – Less than.
* `NUMBER_GT` – Greater than.
* `NUMBER_EQ` – Equal.
* `NUMBER_NEQ` – Not equal.
* `NUMBER_GEQ` – Greater or equal.
* `NUMBER_LEQ` – Less or equal.
* `STRING_EQ` – Equal.
* `STRING_EQI` – Equal but case-insensitive.
* `STRING_NEQ` – Not equal.
* `STRING_NEQI` – Not equal case-insensitive.
* `LOGIC_OR` – Union used if A and B are conditions.
* `LOGIC_AND` – Intersection used if A and B are conditions.
* `IS_DEFINED` – Check for value presence. Only left operand is required - `(car IS_DEFINED)`.
* `NOT_DEFINED` – Check for value absence. Only left operand is required - `(car NOT_DEFINED)`.

**Warning**: If you use `(pet STRING_EQ 'dog')` and contact custom field pet is not defined then the statement will evaluate to false. In most cases this is what you mean, but it is better to use additional `IS_DEFINED` operator to keep logic clean.

**Warning**: If you apply `NUMBER_*` operator to not numeric value it will evaluate to false.

**Warning**: If custom field in condition is multi value then conditional statement will evaluate to true if any element matches it. So if custom pet has values "mouse" and "hamster" then `(pet STRING_EQ 'mouse')` is true because "mouse" value meets the condition, but also `(pet STRING_NEQ 'mouse')` is true because "hamster" value meets the condition.

Mangling:

Mangling allows you to use contact custom field name or token name as left operand without having to worry about its tag type. For example if you use `(city STRING_EQI 'Gdańsk')` then:

1. Value of [contact custom field](#contact_custom_fields) city is checked. If it exists conditional statement is resolved to true or false at this stage.
2. City is also token in [contact geo location](#contact_geo_location). If it exists and conditional statement is not yet resolved then conditional statement is resolved to true or false at this stage.
3. Conditional statement is false if not yet resolved.

Presence of custom of given name is always checked before presence of such named token in other tags.

---

####external content

Include content from any WWW server in message.

```
{{EXTERNAL "http://realestate.com/index.html?location={{GEO "city"}}&type={{CUSTOM "interested_in"}}&"}}
```

When message is sent

1.	Link is evaluated.
2.	External content is downloaded from this link.
3.	Content in placed in message instead of tag.

**Hint**: External content can be provided as plain link. But this may not be enough when you want to personalize it and serve different content for every contact. Luckily you can parametrize link with [campaign predefined values](#campaign_predefined_values), [contact custom field](#contact_custom_fields), [campaign or contact or message_info](#campaign_or_contact_or_message_info) or [contact geo location](#contact_geo_location) tags inside URL.
Just nest corresponding tags in your external link (they cannot have default values or beautifulizers).

For example if you want to serve different product of the day offer for ladies and gentlemen and your contacts have custom field "sex" with values "F" and "M" then place in your message.

```
Hello, this is our product of the day for you:
{{EXTERNAL "http://mypage.com/product_of_the_day.htm?who={{CUSTOM "sex"}}"}}
```

Ladies will get offer obtained from `http://mypage.com/product_of_the_day.htm?who=F` while gentlemen will get offer obtained from `http://mypage.com/product_of_the_day.htm?who=M`.

Nested tags have following restrictions:

* Can not contain default value - `{{CONTACT "subscriber_name" "Friend"}}` is forbidden.
* Can not use [beautifulizers](#beautifulizers) - `{{CONTACT "uc(subscriber_name)"}}` is forbidden.
* Are encoded in UTF-8 as as URL param, so if `{{CONTACT "subscriber_name"}}` is "Pabian Paweł" it will be inserted into link as "Pabian%20Pawe%C5%82" to make link valid. Please note that "/" character will also be encoded as "%2F" so do not use nested tag as part of your link path.


**Hint**: You can include many external tags in single message.

```
Hi

This is what you may need for your pet
{{EXTERNAL "http://pet.store.com/?animal={{CUSTOM"pet"}}&limit=1"}}

And you may check our other products
{{EXTERNAL "http://pet.store.com/general_offer"}}
```

**Hint**: You can use every Dynamic Content tag in content returned from your server.

For external `{{EXTERNAL "http://me.com/info"}}` your server can return 

```
Hi {{CONTACT "ucfw(subscriber_first_name)"}}!

We are international company.

{{LINK "http://company.com"}}
```

This response will be re-evaluated (including click tracking), so following content will be inserted into the message.

```
Hi Paweł!

We are international company.

http://getresponse.com/click.html?x=a62a&..
```

**Warning**: If nested Dynamic Content tag value is missing then empty string will be used in link. This may in rare cases result in incorrect link. For example if you queue message with external `{{EXTERNAL "http://{{PREDEFINED "my_business_domain"}}/offer.html"}}` and then remove my_domain predefine before message is sent it will break the link causing `http:///offer.html` to be external source content instead of `http://my-site.com/offer.html`.

**Warning**: Be aware of message content type. If you send combo (plain and HTML) messages your external content source should also be able to return plain and HTML. For example use in plain part of the message `{{EXTERNAL "http://about.me/main?type=plain"}}` and in HTML part of the message use `{{EXTERNAL "http://about.me/main?type=html"}}` and serve different content from your server depending on type param value.

**Warning**: Do not return www pages from your server, only content to be inserted into the message! This is very important! If you return whole www page with CSS styles, <head> and <body> section and <javascript> code then this probably will not work because of broken message structure and limited capabilities of email clients.

Limitations:

* External content maximum size is 64KB per one tag.
* External content download timeout is 4s.

Error handling:

What may go wrong when using external content?

* Link is not responding (timeout or response code 4xx/5xx).
* Link is incorrect after evaluation (explained above).
* Downloaded content has incorrect Dynamic Content syntax.

In such cases evaluated link will be inserted into the message.

##BEAUTIFULIZERS

Several beautifulizers are allowed in some tags:

* `lc()` – Lowercase all characters. ComPUTer will be inserted into message as computer.
* `uc()` – Uppercase all characters. coMPuTEr will be inserted into message as COMPUTER.
* `ucfw()` – Uppercase first letters in each word and lowercase other. o'reilly bOOk PUBLISHING will be inserted into message as O'Reilly Book Publishing. Note that this beautifulizer also recognizes parts that should not be uppercased. Ludwig VAN beethoven Jr will be inserted as Ludwig van Beethoven jr. Current list of exceptions consists of: von, van, bin, ibn, mgr, dr, prof, imc, jr and may be extended if needed.
* `ucfs()` – Uppercase first letter of every sentence and lowercase other letters. how ARE you? i'm FINE. will be inserted into message as How are you? I'm fine..

For polish users declensions are available for names.

*	`vocative()` – Create vocative form of name, Tomek will be inserted into message as Tomku.

Beautifulizers can be nested. For example if

```
{{CONTACT "subscriber_first_name"}}
```
inserts paweł then

```
{{CONTACT "ucfw(vocative(subscriber_first_name))"}}
```
will insert Pawle.

Beautifulizers are multi-value aware. For example if

```
{{CUSTOM "car"}}
```
inserts honda, toyota, dodge then

```
{{CUSTOM "ucfw(car)"}}
```

will insert Honda, Toyota, Dodge.

##CHANGELOG<a name="changelog">

version 1.6.6, 2012-11-06

* [system links](#system_links) has `me` type added which replaced obsolete `unsubscribe` and `change_details` types

version 1.6.5, 2012-08-03

* [social links](#social_links) action is removed from facebook and twitter services

version 1.6.4, 2012-05-30

* [social links](#social_links) allows action to be specified in facebook and twitter services, buzz is replaced with googleplus

version 1.6.3, 2012-03-09

* [campaign or contact or message info](#campaign_or_contact_or_message_info) accepts `message_reply_to_email` token

version 1.6.2, 2012-01-19

* [contact geo location](#contact_geo_location) got new `continent_code` and `time_zone` tokens, also usable in [conditions](#conditions)

version 1.6.1, 2011-11-15

* [campaign or contact or message info](#campaign_or_contact_or_message_info) accepts default fallback value

version 1.6.0, 2011-07-14

* nested [beautifulizers](#beautifulizers) are allowed
- added declension [beautifulizers](#beautifulizers) (for polish users)
- [beautifulizers](#beautifulizers) are multi value aware
- percent char allowed in [dates](#dates) and [contact subscription date](#contact_subscription_date) tag params
- fixed arity checks for [clicktracked links](#clicktracked_links) tags
- fixed nested [campaign predefined values](#campaign_predefined_values), [contact custom fields](#contact_custom_fields) and [contact geo location](#contact_geo_location) behavior in [clicktracked links](#clicktracked_links) when message is not click tracked
- [conditions](#conditions) are aware of multi value operands and always use junction "any" to evaluate conditional statement, other junctions are removed
- [conditions](#conditions) always require not constant operand to be on the left side and constant operand on the right side of conditional statement
- [conditions](#conditions) operand names must be lowercased
- [campaign or contact or message info](#campaign_or_contact_or_message_info) mangling is added for [conditions](#conditions)
- operator `STRING_NEQI` is available in [conditions](#conditions)
- `longitude` and `latitude` tokens added in [contact geo location](#contact_geo_location) tags
- all translations services are removed, including `{{TRANSLATE}}` tag and `_TRANSLATED` tokens

version 1.5.0, 2011-04-05

- added [qrcode links](#qrcode_links)

version 1.4.0, 2011-03-10

- added tag `{{TRANSLATE}}` for automatic translation of text based on their GeoIP country code

version 1.3.2, 2011-03-07

- [campaign or contact or message info](#campaign_or_contact_or_message_info) tag has new `campaign_id`, `message_id` and `subscriber_id` tokens which return the same `IDs` as obtainable from API

version 1.3.1, 2011-02-22

- [campaign or contact or message info](#campaign_or_contact_or_message_info) tag has new `campaign_reply_to_email`, `message_from_name` and `message_from_email` tokens