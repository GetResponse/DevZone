BEGIN { unshift @*INC, 'lib' }

use Test;
use GetResponse::DynamicContent::Grammar;

my $parsed;

my %good = (
    'empty content' => '',
    'plain text' => 'test',
);

for %good.kv -> $description, $to_parse {
    ok GetResponse::DynamicContent::Grammar.parse($to_parse), $description;
}

