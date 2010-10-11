If you have any questions please write directly to: 

Ben Tadiar
ben@bentadiar.co.uk
www.assemblystudios.co.uk
http://github.com/BenTheDesigner



## GetResponsePHP

GetResponsePHP is a PHP5 implementation of the [GetResponse API][]

### Requirements

* PHP >= 5.2.14 (Not tested under earlier releases)
* [PHP cURL]

### Release Notes

Around 50% of API methods have been implemented with the remainder to follow.

### Basic Usage

#### Include and instantiate the API:

	require_once('GetResponseAPI.class.php');
	$api = new GetResponse('YOUR_API_KEY');

#### Test connection to the API:

	echo $api->ping(); // Output: "pong"
	
#### Get basic account information:

	$account = $api->getAccountInfo();
	var_dump($account);

[PHP cURL]: http://php.net/manual/en/book.curl.php
[GetResponse API]: http://dev.getresponse.com/api-doc/
