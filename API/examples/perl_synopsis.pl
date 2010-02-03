#!/usr/bin/perl

use strict;
use warnings;

=head1 DESCRIPTION

Implementation of sample scenario using GetResponse API:

Add new contact to campaign 'sample_marketing'.
Start his follow-up cycle and set custom field
'last_purchased_product' to 'netbook'.

=head1 AUTHOR

Pawel Pabian
http://implix.com
http://dev.getresponse.com

=cut

use Data::Dumper;

# JSON::RPC module is required
# available at http://search.cpan.org/perldoc?JSON%3A%3ARPC
use JSON::RPC::Client;

# your API key
# available at http://www.getresponse.com/my_api_key.html
my $api_key = 'ENTER_YOUR_API_KEY_HERE';

# API 2.x URL
my $api_url = 'http://api2.getresponse.com';

# initialize JSON-RPC client
my $client = JSON::RPC::Client->new();

# get CAMPAIGN_ID of 'sample_marketing' campaign
my $response = $client->call(
    $api_url,
    {   'method' => 'get_campaigns',
        'params' => [
            $api_key,
            {

                # find by name literally
                'name' => {'EQUALS' => 'sample_marketing'}
            }
        ]
    }
);

# check for communication and response errors
# implement handling if needed
die $client->status_line unless $response;
die $response->error_message if $response->is_error;

# uncomment this line to preview data structure
# print Dumper $response->result;

# since there can be only one campaign of this name
# first key is the CAMPAIGN_ID you need
my $CAMPAIGN_ID = [keys %{$response->result}]->[0];

# add contact to 'sample_marketing' campaign
$response = $client->call(
    $api_url,
    {   'method' => 'add_contact',
        'params' => [
            $api_key,
            {

                # campaign to which contact should be added
                'campaign' => $CAMPAIGN_ID,

                # basic required info
                'name'  => 'Sample Name',
                'email' => 'sample@email.com',

                # contact should begin follow-up cycle
                'cycle_day' => '0',

                # custom field
                'customs' => [
                    {   'name'    => 'last_purchased_product',
                        'content' => 'netbook'
                    },
                ]
            }
        ]
    }
);

# check for communication and response errors
# implement handling if needed
die $client->status_line unless $response;
die $response->error_message if $response->is_error;

# uncomment this line to preview data structure
# print Dumper $response->result;

print "Contact added\n";
