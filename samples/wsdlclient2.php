<?php
/*
 *	$Id: wsdlclient2.php,v 1.4 2010/04/29 13:28:10 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL proxy
 *	Payload: rpc/encoded
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';
$useCURL = $_POST['usecurl'] ?? '0';

$AWSAccessKeyId = 'Your AWS Access Key ID';
$AWSSecretAccessKey = 'Your AWS Secret Access Key';

$client = new nusoap_client("http://ecs.amazonaws.com/AWSECommerceService/2009-11-01/AWSECommerceService.wsdl", 'wsdl',
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	exit();
}
$client->setUseCurl($useCURL);
$proxy = $client->getProxy();
$timestamp = timestamp_to_iso8601(time(), true);
// Note: use of hash_hmac restricts this to PHP 5.1.2 and later
$signature = base64_encode(hash_hmac("sha256", 'BrowseNodeLookup' . $timestamp, $AWSSecretAccessKey, true));
$proxy->setHeaders(
	"<aws:AWSAccessKeyId xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$AWSAccessKeyId</aws:AWSAccessKeyId>\n" .
	"<aws:Timestamp xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$timestamp</aws:Timestamp>\n" .
	"<aws:Signature xmlns:aws=\"http://security.amazonaws.com/doc/2007-01-01/\">$signature</aws:Signature>"
);
$BrowseNodeLookupRequest[] = [
	'BrowseNodeId' => 18,
//	'ResponseGroup' => 'whatever'
];
$BrowseNodeLookup = [
	'AWSAccessKeyId' => $AWSAccessKeyId,
//	'AssociateTag' => '',
	'Request' => $BrowseNodeLookupRequest
];
$result = $proxy->BrowseNodeLookup($BrowseNodeLookup);
// Check for a fault
if ($proxy->fault) {
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
} else {
	// Check for errors
	$err = $proxy->getError();
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
echo '<h2>Request</h2><pre>' . htmlspecialchars($proxy->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($proxy->response, ENT_QUOTES) . '</pre>';
echo '<h2>Client Debug</h2><pre>' . htmlspecialchars($client->debug_str, ENT_QUOTES) . '</pre>';
echo '<h2>Proxy Debug</h2><pre>' . htmlspecialchars($proxy->debug_str, ENT_QUOTES) . '</pre>';
?>
