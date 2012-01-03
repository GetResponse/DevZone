#!/usr/bin/ruby

# Implementation of sample scenario using GetResponse API:
#
# Add new contact to campaign 'sample_marketing'.
# Start his follow-up cycle and set custom field
# 'last_purchased_product' to 'netbook'.
#
# @author Sebastain Nowak, Pawel Pabian
# http://implix.com
# http://dev.getresponse.com
#
# It is highly recommended to use 'getresponse' rubygem for dealing with GetResponse API.
# Rubygem is far better solution than this sample scenario. 'getresponse' rubygem is created
# by Sebastian Nowak, but any other contributors are welcome.
# Source code: https://github.com/seban/ruby-getresponse
# Gem page: https://rubygems.org/gems/getresponse
# Some auto-generated docs: http://rubydoc.info/gems/getresponse/frames

require 'rubygems'
require 'net/http'
require 'json'

# your API key
# available at http://www.getresponse.com/my_api_key.html
api_key = 'ENTER_YOUR_API_KEY_HERE'

# API 2.x URL
api_url = 'http://api2.getresponse.com/'

# initialize JSON-RPC client
uri = URI.parse(api_url)
client = Net::HTTP.start(uri.host, uri.port)

# get CAMPAIGN_ID of 'sample_marketing' campaign
response = client.post(
    uri.path, {
        'method' => 'get_campaigns',
        'params' => [
            api_key, {
                'name' => { 'EQUALS' => 'sample_marketing' }
            }
        ]
    }.to_json
)
# check for communication and response errors
# implement handling if needed

result = JSON.parse(response.body)

# uncomment this line to preview data structure
# print result.inspect

# since there can be only one campaign of this name
# first key is the CAMPAIGN_ID you need
CAMPAIGN_ID = result['result'].keys().pop();

# add contact to 'sample_marketing' campaign
response = client.post(
    '/', {
        'method' => 'add_contact',
        'params' => [
            api_key, {
                'campaign'  => CAMPAIGN_ID,
                'name'      => 'Sample Name',
                'email'     => 'sample@email.com',
                'cycle_day' => '0',
                'customs' => [
                    {
                        'name'      => 'last_purchased_product',
                        'content'   => 'netbook'
                    }
                ]
            }
        ]
    }.to_json
)
# check for communication and response errors
# implement handling if needed

result = JSON.parse(response.body)

# uncomment this line to preview data structure
# print result.inspect

print "Contact added\n";
