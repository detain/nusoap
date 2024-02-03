<?php
/*
 *	$Id: wsdlclient3.php,v 1.4 2007/11/06 14:48:49 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: rpc/encoded
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';
$client = new nusoap_client('http://www.scottnichol.com/samples/hellowsdl2.php?wsdl&debug=1', 'wsdl',
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}
$person = ['firstname' => 'Willi', 'age' => 22, 'gender' => 'male'];
$method = $_GET['method'] ?? 'function';
if ($method == 'function') {
	$call = 'hello';
} elseif ($method == 'instance') {
	$call = 'hellowsdl2.hello';
} elseif ($method == 'class') {
	$call = 'hellowsdl2..hello';
} else {
	$call = 'hello';
}
$result = $client->call($call, ['person' => $person]);
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
?>
