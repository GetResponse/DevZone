#!/usr/bin/python

"""

Implementation of sample scenario using GetResponse API:

Add new contact to campaign 'sample_marketing'.
Start his follow-up cycle and set custom field
'last_purchased_product' to 'netbook'.

Author:
Pawel Pabian
http://implix.com
http://dev.getresponse.com

"""

import pprint
import sys

# JSON-RPC module is required
# available at http://json-rpc.org/wiki/python-json-rpc
from jsonrpc import ServiceProxy

# your API key
# available at http://www.getresponse.com/my_api_key.html
api_key = 'ENTER_YOUR_API_KEY_HERE'

# API 2.x URL
api_url = 'http://api2.getresponse.com'

# initialize JSON-RPC client
client = ServiceProxy(api_url)

result = None

# get CAMPAIGN_ID of 'sample_marketing' campaign
try:
    result = client.get_campaigns(
        api_key,
        {
            # find by name literally
            'name' : { 'EQUALS' : 'sample_marketing' }
        }
    )
except Exception, e:
    # check for communication and response errors
    # implement handling if needed
    #
    # detailed JSONRPCException and JSONDecodeException
    # are also available in jsonrpc package
    sys.exit(e)

# uncomment this line to preview data structure
# pprint.pprint(result)

# since there can be only one campaign of this name
# first key is the CAMPAIGN_ID you need
CAMPAIGN_ID = result.keys().pop();

# add contact to 'sample_marketing' campaign
try:
    result = client.add_contact(
        api_key,
        {
            'campaign' : CAMPAIGN_ID,
            'name' : 'Sample Name',
            'email' : 'sample@email.com',
            'cycle_day' : '0',
            'customs' : [
                {
                    'name'       : 'last_purchased_product',
                    'content'    : 'netbook'
                }
            ]
        }
    )
except Exception, e:
    # check for communication and response errors
    # implement handling if needed
    #
    # detailed JSONRPCException and JSONDecodeException
    # are also available in jsonrpc package
    sys.exit(e)

# uncomment this line to preview data structure
# pprint.pprint(result)

print "Contact added";
