<?php
/**
 * JSports - updatestandings.php
 * This script is designed to be used in a cron job to refresh the standings page.
 *
 * NOTE:  This script is based on the code originally written by Christopher Mavros
 * ( Mavrosxristoforos.com ) and used in his NSPro Joomla extension.
 * 
 */

parse_str(implode('&', array_slice($argv, 1)), $_GET);
$salt = $_GET['salt'];
$resultstring='';

$site = 'https://swibl.org';
$bkey = md5('JSports Key For: ' . $site . $salt); 

$url = $site.'/index.php?option=com_jsports&task=batch.updatestandings&validationid=' . $bkey;

    if (function_exists('curl_init')) {
        // https://swibl.org/index.php?option=com_jsports&task=refreshStandings
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "&validationid=".$bkey);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    	curl_setopt($ch, CURLOPT_VERBOSE, false);
        $curl_response = curl_exec($ch);
        curl_close($ch);
        $resultstring = 'Rereshing standings ' . "\n" . $curl_response;
    }
    else {
        require_once('HTTP/Request3.php');
        $r = new HTTP_Request2($url, HTTP_Request2::METHOD_POST);
	//$r->addPostParameter('validationid', $bkey);
        $page = $r->send();
        $resultstring .= 'Refreshing standings ' . "\n". 'Result: ' . $page->getBody() . "\n\n";
    }

if (!file_exists('cronresults')) { mkdir('cronresults'); }
$file = fopen('cronresults/updatestandings' . date('Y-m-d') . '.txt', "a");
fwrite($file, $resultstring);
fclose($file);

?><!DOCTYPE html>
<head>
<meta charset="utf-8"/>
<title>JSports - Update Standings CRON Results</title>
<style type="text/css">
  .pre { background-color: #F9F9F9; border: 1px solid #D5D5D5; }
</style>
</head>
<body>
<h1>JSports Cron Job Results</h1>
<p>Results: </p>
<pre><?php print $resultstring; ?></pre>
</body>
</html>

