<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>AWS Test</title>
</head>

<body>
<h1>AWS Tests</h1>
<p>Bucket Creation</p>
<?php 
require_once('/home/silver/php/Crypt/HMAC2.php');
require_once('/home/silver/php/HTTP/Request.php');


define("S3URL", 'http://s3.amazonaws.com');
define("AWSACCESSKEYID", 'REDACTED');
define("AWSSECRETKEYID", 'REDACTED');

$s3_hasher =& new Crypt_HMAC2(AWSSECRETKEYID, "sha1");

function s3_sign($StringToSign)
{
    global $s3_hasher;
    return hex2b64($s3_hasher->hash($StringToSign));
}

function hex2b64($str)
{
    $raw = '';
    for ($i = 0; $i < strlen($str); $i += 2) {
        $raw .= chr(hexdec(substr($str, $i, 2)));
    }
    return base64_encode($raw);
}

function setAuthorizationHeader($request)
{
    $headers = $request->_requestHeaders;
    
    $HTTP_Verb      = $request->_method;
    $Content_MD5    = $headers['content-md5'];
    $Content_Type   = $headers['content-type'];

    // Get the date, or set it if not already:
    if (!isset($headers['date'])) {
        $Date = gmdate("D, d M Y H:i:s T");
        $request->addHeader("date", $Date);
    }
    else {
        $Date = $headers['date'];
    }
    
    // Canonicalize the Amazon headers:
    $CanonicalizedAmzHeaders = '';
    $amz_headers = array();
    foreach ($headers as $key => $value) {
        if (substr($key, 0, 6) == 'x-amz-') {
            if (isset($amz_headers[$key]))
                $amz_headers[$key] .= ',' . $value;
            else
                $amz_headers[$key] = $value;
        }
    }
    ksort($amz_headers);
    foreach ($amz_headers as $key => $value)
        $CanonicalizedAmzHeaders .= $key . ':' . $value . "\n";
    
    // Canonicalize the resource string
    $CanonicalizedResource    = '';
    $host = $request->_generateHostHeader();
    if ($host != 's3.amazonaws.com') {
        $pos = strpos($host, 's3.amazonaws.com');
        $CanonicalizedResource .= '/' . ($pos === false) ? $host : substr($host, 0, $pos);
    }
    $CanonicalizedResource .= $request->_url->path;
    // TODO: sub-resources "?acl", "?location", "?logging", or "?torrent"

    // Build the string to sign:
    $StringToSign = $HTTP_Verb . "\n" .
                    $Content_MD5 . "\n" .
                    $Content_Type . "\n" .
                    $Date . "\n" .
                    $CanonicalizedAmzHeaders .
                    $CanonicalizedResource;
    
    $Signature = s3_sign($StringToSign);
    
    $Authorization = "AWS" . " " . AWSACCESSKEYID . ":" . $Signature;
    
    // Set the Authorization header:
    $request->addHeader("Authorization", $Authorization);
}

function s3AuthURL($Resource, $HTTP_Verb = 'GET', $seconds = 120)
{
    // Calculate expiration time:
    $Expires = time() + $seconds;

    // Build the string to sign:
    $StringToSign = $HTTP_Verb . "\n" .
                    "\n" .
                    "\n" .
                    $Expires . "\n" .
                    $Resource;

    $Signature = s3_sign($StringToSign);

    // Build the authorized URL:
    return  S3URL . $Resource .
            '?AWSAccessKeyId='  . AWSACCESSKEYID .
            '&Expires='         . $Expires .
            '&Signature='       . urlencode($Signature);
}


$req =& new HTTP_Request("http://s3.amazonaws.com/[sacred-harp-tunes-organizer]");
$req->setMethod("PUT");
setAuthorizationHeader($req);
$req->sendRequest();

if ($req->getResponseCode() == 200) {
    // bucket was created
	echo "<p>bucket was created</p>";
} else {
    // something bad happened
	echo "<p>something bad happened: ".$req->getResponseCode()."</p>";
}

?>
</body>
</html>
