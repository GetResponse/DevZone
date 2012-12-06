use v6;

# Demonstrates how to add new contact to campaign.

# JSON::RPC module is required
# available at https://github.com/bbkr/jsonrpc
# or included in Rakudo Star distribution from https://github.com/rakudo/star
use JSON::RPC::Client;

# your API key is available at
# https://app.getresponse.com/my_api_key.html
my $api_key = 'ENTER_YOUR_API_KEY_HERE';

# API 2.x URL
my $api_url = 'http://api2.getresponse.com';

# initialize JSON-RPC client
my $client = JSON::RPC::Client.new( url => $api_url );

# find campaign named 'test'
my $campaigns = $client.get_campaigns(
    $api_key,
    {
        # find by name literally
        'name' => {'EQUALS' => 'test'}
    }
);

# uncomment this line to preview Response
# note $campaigns.perl;

# because there can be only one campaign of this name
# first key is the CAMPAIGN_ID required by next method
# (this ID is constant and should be cached for future use)
my $CAMPAIGN_ID = $campaigns.keys.pop;

# add contact to the campaign
my $response = $client.add_contact(
    $api_key,
    {
        # identifier of 'test' campaign
        'campaign' => $CAMPAIGN_ID,

        # basic info
        'name'  => 'Test',
        'email' => 'test@test.com',

        # custom fields
        'customs' => [
            {   'name'    => 'likes_to_drink',
                'content' => 'tea'
            },
            {   'name'    => 'likes_to_eat',
                'content' => 'steak'
            }
        ]
    }
);

# uncomment this line to preview Response
# note $response.perl;

say 'Contact added';

# Pawel Pabian http://implix.com
