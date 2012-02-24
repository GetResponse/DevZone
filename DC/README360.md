##Dynamic Content Documentation Extension for GetResponse360
Dynamic Content version 1.6.0 (2011-07-14) extension 0.1

##AUTHORS
If you wish to contact GetResponse Developer Zone team, please use the following contact form.

##DESCRIPTION
Dynamic Content extension available for GetResponse 360 users.

##TAGS
*	external content

####external content
Include external content in message.

#####Synopsis:

```json
{{EXTERNAL "http://shop.com"}}
{{EXTERNAL "http://realestate.com/index.html?location={{GEO "city"}}&type={{CUSTOM "interested_id"}}&"}}
```

#####How it works:

When message is sent

1.	Link is calculated.
2.	External content is downloaded from this link.
3.	Content in placed in message instead of tag.

#####Usage:

**Hint**: External content can be provided as plain link. But this may not be enough when you want to personalize it and serve different content for every contact. Luckily you can parametrize link with:

*	campaign predefined values
*	campaign or contact or message info
*	contact custom fields
*	contact geo location

Just nest corresponding tags in your external link (they cannot have default values or beautifulizers).

For example if you want to serve different product of the day offer for ladies and gentlemen and your contacts have custom field “sex” with values “F” and “M” added then place in your message…

```json
Hello, this is our product of the day for you:
{{EXTERNAL "http://mypage.com/product_of_the_day.htm?who={{CUSTOM "sex"}}"}}
```

…and ladies will get offer obtained from….

```json
http://mypage.com/product_of_the_day.htm?who=F
```

…while  gentlemen will get offer obtained from…

```json
http://mypage.com/product_of_the_day.htm?who=M
```

**Hint**: You can nest more than one Dynamic Content tags in your external link to personalize it.

```json
{{EXTERNAL "{{PREDEFINED "my_shop_page"}}/{{GEO "country_code"}}"}}
```

…for polish contacts will download content from…

```json
http://my_shop.com/pl
```

And of course it is possible to nest many tags of the same type.

**Hint**: You can include many external tags in single message.

```json
Hi

This is what you may need for your pet
{{EXTERNAL "http://pet.store.com/?animal={{CUSTOM"pet"}}&limit=1"}}

And you may check our other products
{{EXTERNAL "http://pet.store.com/general_offer"}}
```

**Hint**: You can use every Dynamic Content tag in content returned from your server. For example you can place in message…

```json
{{EXTERNAL "http://me.com/info"}}
```

…and your server can return…

```json
Hi {{CONTACT "ucfw(subscriber_first_name)"}}!
```

We are international company.

```json
{{LINK "http://company.com"}}
```

…this response will be re-evaluated (including clicktracking) and…

```json
Hi Paweł!
We are international company.

http://getresponse.com/click.html?x=a62a&..
```

…will be inserted into the message.

**Warning**: If nested Dynamic Content tag value is missing then empty string will be used in link. This may in rare cases result in incorrect link. For example if you queue message with external…

```json
{{EXTERNAL "http://{{PREDEFINED "my_business_domain"}}/offer.html"}}
```

…and then remove my_domain predefine before message is sent it will break the link causing…
```json
http:///offer.html
```

…to be external source content instead of…

```json
http://my_business.com/offer.html
```

**Warning**: Be aware of message content type. If you send combo (PLAIN + HTML) messages your external content source should also be able to return PLAIN and HTML.

For example use in `PLAIN` part of the message…

```json
{{EXTERNAL “http://about.me/main?type=plain”}}
```

…and in `HTML` part of the message…

```json
{{EXTERNAL “http://about.me/main?type=html”}}
```

…and serve different content depending on type param.

**Warning**: Do not return www pages from your server, only content to be inserted into the message! This is very important! If you return whole www page with CSS styles, <head> and <body> section and <javascript> code then this probably won’t work because of broken message structure and limited capabilities of email clients.

#####Limitations:

External content maximum size is 64KB per one tag.

External content download timeout is 4s.

#####Error handling:

What may go wrong when using external content?

*	link won’t respond (timeout or response code 4xx/5xx)
*	link will be incorrect (explained above)
*	downloaded content will have incorrect Dynamic Content syntax

In such cases calculated link will be inserted into the message.