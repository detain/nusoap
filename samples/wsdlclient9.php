<?php
/*
 *	$Id: wsdlclient9.php,v 1.2 2007/11/06 14:50:06 snichol Exp $
 *
 *	WSDL client sample.
 *
 *	Service: WSDL
 *	Payload: document/literal
 *	Transport: http
 *	Authentication: digest
 */
require_once('../lib/nusoap.php');
$proxyhost = $_POST['proxyhost'] ?? '';
$proxyport = $_POST['proxyport'] ?? '';
$proxyusername = $_POST['proxyusername'] ?? '';
$proxypassword = $_POST['proxypassword'] ?? '';
echo 'You must set your username and password in the source';
exit();
$client = new nusoap_client("http://staging.mappoint.net/standard-30/mappoint.wsdl", 'wsdl',
                        $proxyhost, $proxyport, $proxyusername, $proxypassword);
$err = $client->getError();
if ($err) {
    echo '<h2>Constructor error</h2><pre>' . $err . '</pre>';
}
$client->setCredentials($username, $password, 'digest');
$client->useHTTPPersistentConnection();
$view = [
    'Height' => 200,
    'Width' => 300,
    'CenterPoint' => [
        'Latitude' => 40,
        'Longitude' => -120
    ]
];
$myViews[] = new soapval('MapView', 'ViewByHeightWidth', $view, false, 'http://s.mappoint.net/mappoint-30/');
$mapSpec = [
    'DataSourceName' => "MapPoint.NA",
    'Views' => ['MapView' => $myViews]
];
$map = ['specification' => $mapSpec];
$result = $client->call('GetMap', ['parameters' => $map]);
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
