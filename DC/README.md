#GetResponse Dynamic Content

version 1.6.2, 2012-01-19 [changelog](#changelog)

##INFO

Dynamic Content allows to personalize emails.

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
* [externals](#externals)

##GENERAL RULES

Dynamic Content is a programing language, and so it requires strict syntax:

There can be only one instruction per tag.
Syntax is case-sensitive, casing is like in the examples.
Back-ticks can be used instead of double quotes when composing HTML messages.

##TAGS

####campaign predefined values<a name="campaign_predefined_values"/>

Merge-words that are constant in your campaign.

```
{{PREDEFINED "my_shop_name"}}
```

[Beautifulizers](#beautifulizers) are allowed.

Predefined values can be defined on [GetResponse WWW](https://app.getresponse.com/campaigns_predefined_variables.html). Empty string is inserted into the message on undefined value.

---

####campaign or contact or message info<a name="campaign_or_contact_or_message_info"/>

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

####contact custom fields<a name="contact_custom_fields"/>

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

####contact geo location<a name="contact_geo_location"/>

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

####contact subscription date<a name="contact_subscription_date"/>

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

####clicktracked links<a name="clicktracked_links"/>

Mark links for tracking and assign optional description for statistics.

```
{{LINK "http://mysite.com"}}
{{LINK "http://mysite.com/products/1234" "Bone for dog"}}
```

**Warning**: If you edit message using www editor then

Track click-throughs of your links
must be checked to enable clicktracking.

**Warning**: If you send message through API then

```json
    "flag" : [ "clicktrack" ]
```

must be provided to enable clicktracking.

**Hint**: You can use campaign predefined values, campaign or contact or message_info, contact custom fields or contact geo location tags inside your URL (but not inside the description), for example:

```
{{LINK "http://mysite.com/login?id={{CUSTOM "ref"}}"}}
{{LINK "{{PREDEFINED "my_home_page"}}/shop"}}
```

Nested tag cannot contain default value param or beautifulizer!

**Hint**: You can give links a custom description (second param). If description is given it will be shown under this name instead of URL on performance stats. It not only makes stats easier to read, but also makes two tricks possible – splitting and merging.

Splitting allows to show many links with the same URL under different names. It may be useful for statistical purposes like creating a “click heat map” of your message, for example:

```
Hello
{{LINK "http://mysite.com" "My page at the beginning of the message"}}
Some message body
{{LINK "http://mysite.com" "My page at the end of the message"}}
Sincerely
```

Now you know which link was clicked more often because (despite common URL) those links will be shown under separate names on performance stats.

Merging is opposite to splitting – many links with different URLs will be shown under one name on performance stats. This is helpful if you don’t need to distinguish them, for example:

```
{{LINK "http://mysite.com/shop/main" "Main shop page"}}
{{LINK "http://myoldsite.com/redirect/shop" "Main shop page"}}
```

####system links<a name="system_links"/>
Links that allow to perform some system actions.

#####Usage:
```
{{LINK "name"}}
```

#####Supported names and message types those links may be used in:

*	change_details (newsletter, follow-up) – Change contact details. This is the same link as in footer.
*	unsubscribe (newsletter, follow-up) – Allow contact to unsubscribe. This is the same link as in footer.
*	view (newsletter, follow-up) – View message on website. May be useful if contact is using email client that blocks content, for example images.
*	forward (newsletter, follow-up) – Forwards message to a friend.
*	next (follow-up) – Get next message in follow-up cycle without waiting.
*	play (newsletter, follow-up) – Play message using Email 2 Speech feature.

####social links<a name="social_links"/>
Links that allow to post info on social media:

#####Usage:

```
{{LINK "social_*"}}
```

#####Where * can be:

*	buzz  
*	digg
*	facebook
*	linkedin 
*	myspace 
*	twitter 

####qrcode links<a name="qrcode_links"/>
Links that allow to place QR Code into your message.

#####Usage:
```
{{LINK "qrcode" "1234"}}
```

QR Codes can be defined here. Use value from ID column as second tag param.

**Hint**: You can use campaign predefined values, campaign or contact info, contact custom fields or contact geo location tags inside your QR Code content. This way you can generate different QR Code tags for every email sent and create promotions based on pool of defined promo codes.

Nested tag cannot contain default value param or beautifulizer!

####dates<a name="dates"/>
Current date that can be presented in various formats and be time-shifted.

#####Usage:

```
{{DATE "format"}}
{{DATE "format" "modifier"}}
```

#####Formats:

Format part can contain special tokens listed below that will be replaced with date parts.

*	CENTURY – For example 21.
*	YEAR – For example 2010.
*	YEAR_SHORT – For example 10.
*	MONTH – From 01 to 12.
*	MONTH_NAME – From January to December.
*	MONTH_NAME_SHORT – From Jan to Dec.
*	DAY – From 01 to 31.
*	DAY_NAME – From Monday to Sunday.
*	DAY_NAME_SHORT – From Mon to Sun.
*	HOUR – From 00 to 23.
*	MINUTE – From 00 to 59.
*	SECOND – From 00 to 59.

**Warning**: Tokens above are case-sensitive.

Format can also contain every other character except " (double quotes),  ` (backtick) to format date.

**Hint**: You can get rid of leading zeros by suffixing token with _NONZERO, for example:

`HOUR_NONZERO – 0, 1, 2, 3, ..., 22, 23`

**Hint**: You can ordinate token values by suffixing them with `_ORDINATED`, for example:

`HOUR_ORDINATED – 0th, 1st, 2nd, 3rd, ... , 21st`

Ordinated values are stripped of leading zeros.

Ordination respects all exceptions.

**Warning**: You cannot `use_NONZERO` or `_ORDINATED` with naming tokens.

#####Modifiers:

Subscription date can be shifted by given amount of time using modifiers. Modifier must be written as + or - sign, value of time shift and unit of time shift, for example:

*	+1 DAY
*	-2 YEARS

#####Allowed units are:

*	YEAR(S) – 365 days.
*	MONTH(S) – 30 days.
*	DAY(S)
*	HOUR(S)

**Warning**: Total amount of time shift cannot exceed +/-16 years. If it does then unmodified date will be inserted into a message.

#####Examples:

*	```{{DATE "YEAR-MONTH-DAY"}}``` – Will insert 2008-06-24.
*	```{{DATE "DAY_ORDINATED of MONTH_NAME"}}``` – Will insert 11th of July.
*	```{{DATE "DAY_ORDINATED of MONTH_NAME" "+1 DAY"}}``` – Will insert 12th of July.

####timers<a name="timers"/>
Countdown to/since given timestamp or contact subscription date.

#####Usage:

```
{{TIMER "when" "format_future" "format_past"}}
```

#####When can be provided as

*	date+time: YYYY-MM-DD HH:MM:SS
*	date: YYYY-MM-DD
*	contact subscription date: added_on

#####Future / past formats are used if the message was sent before / after given timestamp. Those formats can contain special tokens listed below that will be replaced with time parts:

*	CENTURIES
*	YEARS
*	MONTHS – 30 days.
*	DAYS
*	HOURS
*	MINUTES
*	SECONDS

**Hint**: If you want unit name along with numeric value suffix token with `_UNIT`, for example:

```
{{TIMER "2010-01-01 00:00:00" "HOURS_UNIT" ""}}
```
Will insert 8 hours.

**Hint**: Tokens are greedy. It means that you don’t have to use all of them in formats. Tokens you use will “consume” amount of time in a smart way, for example:

```
{{TIMER "2010-01-01 00:00:00" "HOURS_UNIT" ""}}
```
Will insert 49 hours.

```
{{TIMER "2001-01-01 00:00:00" "DAYS_UNIT and HOURS_UNIT" ""}}
```
Will insert 2 days and 1 hour because DAYS part “consumed” 48 hours.

#####Examples:

```
{{TIMER "2012-01-01 00:00:00" "DAYS_UNIT HOURS_UNIT to the end of the world" "World ended DAYS days ago"}}
```
will insert 705 days 12 hours to the end of the world (at the moment this doc was created) and will insert World ended 32 days ago when it is 32 days after the timestamp date.

```
{{TIMER "added_on" "" "You signed up DAYS_UNIT ago"}}
```
will insert You signed up 35 days ago.

####randoms<a name="randoms"/>
Inserts random text from a provided list.

#####Usage:

```
{{RANDOM "Hi" "Hello" "Hey"}}
```

####currency conversions<a name="currency_conversions"/>

Convert between price currencies in your email depending on which country is your contact from.

#####Usage:

```
{{CURRENCY "amount" "source_currency"}}
```

Source currency must be given as 3-letters code defined in ISO 4217 table.

Target currency is determined by 2-letters `{{CUSTOM "country_code"}}` value or `{{GEO "country_code"}}` if custom field value is not present. Conversion rates are taken from European Central Bank when message is sent.

#####Example:

```
{{CURRENCY "1000" "USD"}}
```
Will insert 1000 USD if contact’s country code is US (or not defined) and will insert 2100 PLN if contact’s country code is PL.

####conditions<a name="conditions"/>

Allows to display various parts of message depending on contact custom fields, contact geo location, campaign or contact or message_info, or dates values.

#####Usage:
```
{{IF "conditions"}}
    text one
{{ELSIF "conditions"}}
    text two
{{ELSIF "conditions"}}
    text three
{{ELSE}}
    text four
{{ENDIF}}
```
#####Syntax rules:

Conditions are defined inside (double quotes).
Constants are defined inside (single quotes), even numeric ones.
Every condition must obey atomic syntax (A operator B)where:

*	A is (always lowercased) custom field name or geo location token or date token or contact token and B is constant value and operator is numeric or string type.
*	A is (always lowercased) custom field name or geo location token or date token or contact token and B does not exists and operator is defined type.
*	A and B are conditions with logic operator type between them.

#####Order of tags:

*	`{{IF "..."}}` – Mandatory beginning of conditional statement.

*	`{{ELSIF "..."}}` – Optional block, multiple allowed.

*	`{{ELSE}}` – Optional default block, one allowed.

*	`{{ENDIF}}` – Mandatory closing of conditional statement.

#####Operators:

*	NUMBER_LT – Less than.
*	NUMBER_GT – Greater than.
*	NUMBER_EQ – Equal.
*	NUMBER_NEQ – Not equal.
*	NUMBER_GEQ – Greater or equal.
*	NUMBER_LEQ – Less or equal.
*	STRING_EQ – Equal.
*	STRING_EQI – Equal but case-insensitive.
*	STRING_NEQ – Not equal.
*	STRING_NEQI – Not equal case-insensitive.
*	LOGIC_OR – Union used if A and B are conditions.
*	LOGIC_AND – Intersection used if A and B are conditions.
*	IS_DEFINED – Check for variable presence. Only A param is required.
*	IS_NOT_DEFINED – Check for variable absence. Only A param is required.

Nesting of conditions is allowed.

#####Examples:

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

**Warning**: If you use `{{IF "(pet STRING_EQ 'dog')"}}` and contact custom field pet is not defined then the statement will evaluate to false. In most cases this is what you mean, but it is better to use additional `IS_DEFINED` operator to keep logic clean.

**Warning**: If you apply `NUMBER_*` operator to not numeric value it will evaluate to false.

**Warning**: If custom field in condition is multivalue then condition will evaluate to true if any element matches it. So if custom pet has values mouse and hamster then

(pet STRING_EQ ‘mouse’)

is true because mouse value meets the condition, but

(pet STRING_NEQ ‘mouse’)

is also true because hamster value meets the condition.

##BEAUTIFULIZERS<a name="beautifulizers"/>

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