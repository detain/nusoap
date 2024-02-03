<?php
/*
 *	$Id: client1.php,v 1.4 2010/04/29 13:28:10 snichol Exp $
 *
 *	Client sample that should get a fault response.
 *
 *	Service: SOAP endpoint
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
$client = new nusoap_client("https://ecs.amazonaws.com/onca/soap?Service=AWSECommerceService", false,
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
	exit();
}
$client->setUseCurl($useCURL);
// This is an archaic parameter list
$params = [
    'manufacturer' => "O'Reilly",
    'page'         => '1',
    'mode'         => 'books',
    'tag'          => 'trachtenberg-20',
    'type'         => 'lite',
    'devtag'       => 'D35PWRR0R0URC3',
    'sort'         => '+title'
];
$result = $client->call('ManufacturerSearchRequest', $params, 'http://soap.amazon.com', 'http://soap.amazon.com');
if ($client->fault) {
	echo '<h2>Fault (Expect - Request must contain AWSAccessKeyId or X.509 certificate.)</h2><pre>'; print_r($result); echo '</pre>';
} else {
	$err = $client->getError();
	if ($err) {
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	} else {
		echo '<h2>Result</h2><pre>'; print_r($result); echo '</pre>';
	}
}
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
?>
