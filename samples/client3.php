<?php
/*
 *	$Id: client3.php,v 1.4 2007/11/06 14:48:24 snichol Exp $
 *
 *	Client sample.
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
$client = new nusoap_client("http://api.google.com/search/beta2", false,
                        $proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
    exit();
}
$client->setUseCurl($useCURL);
$client->soap_defencoding = 'UTF-8';

//echo 'You must set your own Google key in the source code to run this client!'; exit();
$params = [
    'Googlekey'=>'Your Google key',
    'queryStr'=>'robotics',
    'startFrom'=>0,
    'maxResults'=>10,
    'filter'=>true,
    'restrict'=>'',
    'adultContent'=>true,
    'language'=>'',
    'iencoding'=>'',
    'oendcoding'=>''
];
$result = $client->call("doGoogleSearch", $params, "urn:GoogleSearch", "urn:GoogleSearch");
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
echo '<h2>Request</h2><pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2><pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
