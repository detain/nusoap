<?php
/*
 *	$Id: wsdlclient5.php,v 1.4 2007/11/06 14:49:10 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: rpc/encoded
 *	Transport: http
 *	Authentication: none
 */
require_once('../lib/nusoap.php');
require_once('../lib/class.wsdlcache.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';
$useCURL = $_POST['usecurl'] ?? '0';

$cache = new wsdlcache('.', 60);
$wsdl = $cache->get('http://www.xmethods.net/sd/2001/BNQuoteService.wsdl');
if (is_null($wsdl)) {
    $wsdl = new wsdl('http://www.xmethods.net/sd/2001/BNQuoteService.wsdl',
                    $proxyhost, $proxyport, $proxyusername, $proxypassword,
                    0, 30, null, $useCURL);
    $err = $wsdl->getError();
    if ($err) {
        echo '<h2>WSDL Constructor error (Expect - 404 Not Found)</h2><pre>' . $err . '</pre>';
        echo '<h2>Debug</h2><pre>' . htmlspecialchars($wsdl->getDebug(), ENT_QUOTES) . '</pre>';
        exit();
    }
    $cache->put($wsdl);
} else {
    $wsdl->clearDebug();
    $wsdl->debug('Retrieved from cache');
}
$client = new nusoap_client($wsdl, 'wsdl',
                        $proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
    exit();
}
$client->setUseCurl($useCURL);
$params = ['isbn' => '0060188782'];
$result = $client->call('getPrice', $params);
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
echo '<h2>Cache Debug</h2><pre>' . htmlspecialchars($cache->getDebug(), ENT_QUOTES) . '</pre>';
echo '<h2>Debug</h2><pre>' . htmlspecialchars($client->getDebug(), ENT_QUOTES) . '</pre>';
