<?php session_start();
date_default_timezone_set('Asia/Tokyo');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Origin, Accept, Access-Control-Allow-Headers, X-Token");

header('Server: Hidden');
header('X-Powered-By: Hidden');

ini_set("display_errors", 1);
ini_set("error_reporting", E_ALL);

/* Content-Type: form/multipart, application/json 両方に対応 **/
$_SERVER['CONTENT_TYPE'] = (isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:'application/octet-stream';
if($_SERVER['REQUEST_METHOD']=='POST'&&substr(strtolower($_SERVER['CONTENT_TYPE']),0,16)=='application/json'){
	try {
		$_POST = file_get_contents('php://input');
		$_POST = strlen($_POST)>0 ? json_decode($_POST, TRUE, 512, JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR) : [];
	} catch (\JsonException $e) {
		$_POST = null;
		error_log('JSON Parse error: ' . __FILE__ . ':' . __LINE__ . PHP_EOL . $e->getTraceAsString());
	}
}

