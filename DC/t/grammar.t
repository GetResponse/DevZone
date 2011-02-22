BEGIN { unshift @*INC, 'lib' }

use Test;
use GetResponse::DynamicContent::Grammar;

my $parsed;

my %good = (

    'empty content' => '',
    'plain text' => 'test',

    'predefined' => '{{PREDEFINED "test"}}',

    'contact' => '{{CONTACT "subscriber_name"}}',
    'contact with beautifulizer' => '{{CONTACT "lc(subscriber_name)"}}',

    'custom' => '{{CUSTOM "test"}}',
    'custom with default value' => '{{CUSTOM "test" "test"}}',

    'geo' => '{{GEO "city"}}',

    'random single' => '{{RANDOM "test"}}',
    'random multi' => '{{RANDOM "test" "test"}}',

    'if' => '{{IF "1"}}{{ENDIF}}',
    'if elsif' => '{{IF "1"}}{{ELSIF "1"}}{{ENDIF}}',
    'if elsif elsif' => '{{IF "1"}}{{ELSIF "1"}}{{ELSIF "1"}}{{ENDIF}}',
    'if else' => '{{IF "1"}}{{ELSE}}{{ENDIF}}',
    'if elsif else' => '{{IF "1"}}{{ELSIF "1"}}{{ELSE}}{{ENDIF}}',
    'if nested' => '{{IF "1"}}{{IF "1"}}{{ENDIF}}{{ENDIF}}',
);

my %bad = (
    'tag not closed' => '{{',
    'nonexisting tag' => '{{FOO}}',
    'param empty' => '{{CUSTOM ""}}',
    'tag name lowercase' => '{{custom "test"}}',
    'param geo incorrect' => '{{GEO "foo"}}',
);



for %good.kv -> $description, $to_parse {
    ok GetResponse::DynamicContent::Grammar.parse($to_parse), $description;
    # $/.perl.say;
}

for %bad.kv -> $description, $to_parse {
    ok not GetResponse::DynamicContent::Grammar.parse($to_parse), $description;
}

