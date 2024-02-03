<?php
/*
 *	$Id: wsdlclient12.php,v 1.5 2010/04/29 13:28:10 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: document/literal
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
require_once('../lib/class.wsdlcache.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';

$method = $_GET['method'] ?? 'ItemSearch';

$AWSAccessKeyId = 'Your AWS Access Key ID';
$AWSSecretAccessKey = 'Your AWS Secret Access Key';

//$wsdlurl = 'http://webservices.amazon.com/AWSECommerceService/US/AWSECommerceService.wsdl';
$wsdlurl = 'http://ecs.amazonaws.com/AWSECommerceService/2009-11-01/AWSECommerceService.wsdl';
$cache = new wsdlcache('.', 86400);
$wsdl = $cache->get($wsdlurl);
if (is_null($wsdl)) {
    $wsdl = new wsdl($wsdlurl,
                    $proxyhost, $proxyport, $proxyusername, $proxypassword);
    $cache->put($wsdl);
} else {
    $wsdl->debug_str = '';
    $wsdl->debug('Retrieved from cache');
}
$client = new nusoap_client($wsdl, true,
                        $proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}

$client->soap_defencoding = 'UTF-8';

function GetHeaders($action)
{
    global $AWSAccessKeyId;
    global $AWSSecretAccessKey;

    $timestamp = timestamp_to_iso8601(time(), true);
    // Note: use of hash_hmac restricts this to PHP 5.1.2 and later
    $signature = base64_encode(hash_hmac("sha256", $action . $timestamp, $AWSSecretAccessKey, true));
    return
        "<aws:AWSAccessKeyId xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$AWSAccessKeyId</aws:AWSAccessKeyId>\n" .
        "<aws:Timestamp xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$timestamp</aws:Timestamp>\n" .
        "<aws:Signature xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$signature</aws:Signature>";
    ;
}

function GetCartCreateParams()
{
    global $AWSAccessKeyId;

    // create items to be added to the cart
    $item =  [];
    $item[0] = [  "ASIN" => "0596004206",
                       "Quantity" => "1"
                    ];
    $item[1] = [  "ASIN" => "0596003277",
                       "Quantity" => "2"
                    ];

    // pack it to <Item> array
    $items =  ["Item" => $item];
    // Construct request parameters
    $request = ["Items" => $items, "ResponseGroup" => "CartSimilarities"];
    
    // Construct  all parameters
    $cartCreate = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
        "Request" => $request
    ];

    return $cartCreate;
}

function GetItemLookupParams()
{
    global $AWSAccessKeyId;

    $itemLookupRequest[] = [
        'ItemId' => 'B0002IQML6',
        'IdType' => 'ASIN',
        'Condition' => 'All',
        'ResponseGroup' => 'Large'
    ];
    
    $itemLookupRequest[] = [
        'ItemId' => '0486411214',
        'IdType' => 'ASIN',
        'Condition' => 'New',
        'ResponseGroup' => 'Small'
    ];

    $itemLookup = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
    //	'AssociateTag' => '',
        'Request' => $itemLookupRequest
    ];
    
    return $itemLookup;
}

function GetItemSearchParams()
{
    global $AWSAccessKeyId;

    $itemSearchRequest = [
        'BrowseNode' => '53',
        'ItemPage' => 1,
    //	'ResponseGroup' => array('Request', 'Small'),
        'SearchIndex' => 'Books',
        'Sort' => 'salesrank'
    ];
    
    $itemSearch = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
    //	'AssociateTag' => '',
    //	'Validate' => '',
    //	'XMLEscaping' => '',
    //	'Shared' => $itemSearchRequest,
        'Request' => [$itemSearchRequest]
    ];
    
    return $itemSearch;
}

function GetItemSearchParams2()
{
    global $AWSAccessKeyId;

    $request = [
        "Keywords" => "postal stamps",
        "SearchIndex" => "Books"
    ];

    $itemSearch = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
        'Request' => $request
    ];

    return $itemSearch;
}

function GetListLookupParams()
{
    global $AWSAccessKeyId;

    $listLookupRequest[] = [
        'ListId' => '1L0ZL7Y9FL4U0',
        'ListType' => 'WishList',
        'ProductPage' => 1,
        'ResponseGroup' => 'ListFull',
        'Sort' => 'LastUpdated'
    ];
    
    $listLookupRequest[] = [
        'ListId' => '1L0ZL7Y9FL4U0',
        'ListType' => 'WishList',
        'ProductPage' => 2,
        'ResponseGroup' => 'ListFull',
        'Sort' => 'LastUpdated'
    ];
    /*
    // two lookup maximum
        $listLookupRequest[] = array(
            'ListId' => '1L0ZL7Y9FL4U0',
            'ListType' => 'WishList',
            'ProductPage' => 3,
            'ResponseGroup' => 'ListFull',
            'Sort' => 'LastUpdated'
        );
    */
    $listLookup = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
    //	'AssociateTag' => '',
        'Request' => $listLookupRequest,
    ];
    
    return $listLookup;
}

function GetListSearchParams()
{
    global $AWSAccessKeyId;

    $listSearchRequest[] = [
        'FirstName' => 'Scott',
        'LastName' => 'Nichol',
        'ListType' => 'WishList'
    ];
    
    $listSearch = [
        'AWSAccessKeyId' => $AWSAccessKeyId,
    //	'AssociateTag' => '',
        'Request' => $listSearchRequest,
    ];
    
    return $listSearch;
}

if ($method == 'ItemLookup') {
    $result = $client->call('ItemLookup', ['body' => GetItemLookupParams()], '', '', GetHeaders('ItemLookup'));
} elseif ($method == 'ItemSearch') {
    $result = $client->call('ItemSearch', ['body' => GetItemSearchParams()], '', '', GetHeaders('ItemSearch'));
} elseif ($method == 'ItemSearch2') {
    $result = $client->call('ItemSearch', ['body' => GetItemSearchParams2()], '', '', GetHeaders('ItemSearch'));
} elseif ($method == 'ListLookup') {
    $result = $client->call('ListLookup', ['body' => GetListLookupParams()], '', '', GetHeaders('ListLookup'));
} elseif ($method == 'ListSearch') {
    $result = $client->call('ListSearch', ['body' => GetListSearchParams()], '', '', GetHeaders('ListSearch'));
} elseif ($method == 'CartCreate') {
    $result = $client->call('CartCreate', ['body' => GetCartCreateParams()], '', '', GetHeaders('CartCreate'));
} else {
    echo "Unsupported method $method";
    exit;
}
// Check for a fault
if ($client->fault) {
    echo '<h2>Fault</h2><pre>';
    print_r($result);
    echo '</pre>';
} else {
    // Check for errors
    $err = $client->getError();
    if ($err) {
        // Display the error
        echo '<h2>Error</h2><pre>' . $err . '</pre>';
    } else {
        // Display the result
        echo '<h2>Result</h2><pre>';
        print_r($result);
        echo '</pre>';
    }
}
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
