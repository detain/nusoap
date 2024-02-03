<?php
/*
 *	$Id: wsdlclient11.php,v 1.3 2011/01/15 16:00:45 snichol Exp $
 *
 *	WSDL client sample.
 *	Exercises a document/literal NuSOAP service (added nusoap.php 1.73).
 *	Does not explicitly wrap parameters.
 *
 *	Service: WSDL
 *	Payload: document/literal
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';
$useCURL = $_POST['usecurl'] ?? '0';
$client = new nusoap_client('http://www.scottnichol.com/samples/hellowsdl3.wsdl', 'wsdl',
						$proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
	echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
	echo '<h2>Debug</h2>';
	echo '<pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
	exit();
}
$client->setUseCurl($useCURL);
$person = ['firstname' => 'Willi', 'age' => 22, 'gender' => 'male', 'any' => '<hobbies>surfing</hobbies>'];
$name = ['name' => $person];
$result = $client->call('hello', $name);
if ($client->fault) {
	echo '<h2>Fault</h2><pre>';
	print_r($result);
	echo '</pre>';
} else {
	$err = $client->getError();
	if ($err) {
		echo '<h2>Error</h2><pre>' . $err . '</pre>';
	} else {
		echo '<h2>Result</h2><pre>';
		print_r($result);
	echo '</pre>';
	}
}
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
?>
