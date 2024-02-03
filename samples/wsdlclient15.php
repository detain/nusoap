<?php
/*
 *	$Id: wsdlclient15.php,v 1.2 2011/01/15 16:02:02 snichol Exp $
 *
 *	UTF-8 client sample that sends and receives data with characters UTF-8 encoded.
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
$client = new nusoap_client('http://www.scottnichol.com/samples/helloutf8.php?wsdl', 'wsdl',
                        $proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    echo '<h2>Debug</h2>';
    echo '<pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
    exit();
}
$client->setUseCurl($useCURL);
$client->soap_defencoding = 'UTF-8';
$client->decode_utf8 = false;
$client->setHeaders(['Info1' => 'Data #1', 'Info2' => 'Data #2']);	// a test of setHeaders that does not change the behavior
$utf8string = ['stuff' => "\xc2\xa9\xc2\xae\xc2\xbc\xc2\xbd\xc2\xbe"];
$result = $client->call('echoback', $utf8string);
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
        // Decode the result: it so happens we sent Latin-1 characters
        if (isset($result['return'])) {
            $result1 = utf8_decode($result['return']);
        } elseif (!is_array($result)) {
            $result1 = utf8_decode($result);
        } else {
            $result1 = $result;
        }
        print_r($result1);
        echo '</pre>';
    }
}
echo '<h2>Request</h2>';
echo '<pre>' . htmlspecialchars($client->request, ENT_QUOTES) . '</pre>';
echo '<h2>Response</h2>';
echo '<pre>' . htmlspecialchars($client->response, ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2>';
echo '<pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
