<?php session_start();
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include_once(__DIR__ . '/../vendor/autoload.php');
}
date_default_timezone_set('Asia/Tokyo');
header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Content-Length, Origin, Accept, Access-Control-Allow-Headers, X-Token");

header('Server: Hidden');
header('X-Powered-By: Hidden');

/* Content-Type: form/multipart, application/json 両方に対応 **/
$_SERVER['CONTENT_TYPE'] = (isset($_SERVER['CONTENT_TYPE']))?$_SERVER['CONTENT_TYPE']:'application/octet-stream';
if($_SERVER['REQUEST_METHOD']=='POST'&&substr(strtolower($_SERVER['CONTENT_TYPE']),0,16)=='application/json'){
	try {
		$_POST = file_get_contents('php://input');
		$_POST = strlen($_POST)>0 ? json_decode($_POST, TRUE, 512, JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR) : [];
	} catch (\JsonException $e) {
		$_POST = null;
		error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '. 'JSON Parse error: ' . __FILE__ . ':' . __LINE__ . PHP_EOL . $e->getTraceAsString());
	}
}

class ipinfo {
    private $access_token;
    function __construct ($options=[
        'access_token'=>null,
    ]) {
        $this->access_token = $options['access_token'];
        if (!isset($this->access_token) || strlen($this->access_token)==0) {
            error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '. 'Unable the Class ' . get_class(self) . ' Initialize.');
            exit(1);
        }
    }
}
class conoha {
    function __construct ($options=[
        'username'=>null,
        'password'=>null,
        'tenantid'=>null,
    ]) {
        foreach($options as $k => $v) {
            $this->auth[$k] = $v;
            if (!isset($this->auth[$k]) || strlen($this->auth[$k])==0) {
                error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '. 'Unable the Class ' . get_class(self) . ' Initialize. Reason not set ' . $k);
                exit(1);
            }
        }
    }
}
class discord {
    private $access_token;
    function __construct ($options=[
        'access_token'=>null,
    ]) {
        $this->access_token = $options['access_token'];
        if (!isset($this->access_token) || strlen($this->access_token)==0) {
            error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '. 'Unable the Class ' . get_class(self) . ' Initialize.');
            exit(1);
        }
    }
    function getCurrentUser () {
        /* 
        * name: getCurrentUser
        * return: objective-array
        **/
        $api_endpoint = 'https://discordapp.com/api/users/@me';
        $api_customHeader = [
            'Authorization: Bearer ' . $this->access_token,
        ];
        error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '. json_encode([
            $api_endpoint,
            $api_customHeader,
            $_SERVER['REMOTE_ADDR'],
        ], JSON_UNESCAPED_SLASHES));
        
        $curl_req = curl_init();
        curl_setopt($curl_req, CURLOPT_URL, $api_endpoint);
        curl_setopt($curl_req, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl_req, CURLOPT_HTTPHEADER, $api_customHeader);
        curl_setopt($curl_req, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl_req, CURLOPT_FOLLOWLOCATION, TRUE);

        $curl_res=curl_exec($curl_req);
        $curl_res=json_decode($curl_res, TRUE);

        return $curl_res; /* objective-array **/
    }
}

$requests = [];
$requests = array_merge([
    'auth' => [
        'discord' => [
            'access_token' => null,
        ],
        'conoha' => [
            'passwordCredentials' => [
                'username' => null,
                'password' => null,
            ],
            'tenantId' => null,
        ],
        'ipinfo' => [
            'access_token' => null,
        ],
    ],
], $requests);
$requests = array_merge($requests, $_POST);
$discord_api = new discord([
    'access_token'=> $requests['auth']['discord']['access_token'],
]);
if (!(array_merge(['id'=>null], $discord_api->getCurrentUser())['id'])) {
    http_response_code(401);
    error_log('['.__LINE__.'] ['.$_SERVER['REMOTE_ADDR'].'] '.json_encode([
        'httpcode'=>401,
        'description'=>'Unauthorized',
        'remoteaddr'=>$_SERVER['REMOTE_ADDR'],
    ]));
    echo json_encode([
        'httpcode'=>401,
        'description'=>'Unauthorized',
    ], JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR);
    exit(0);
};
$conoha_api = new conoha([
    'username'=>$requests['auth']['conoha']['passwordCredentials']['username'],
    'password'=>$requests['auth']['conoha']['passwordCredentials']['password'],
    'tenantid'=>$requests['auth']['conoha']['tenantId'],
]);

http_response_code(501);
echo json_encode([
    'httpcode'=>501,
    'description'=>'Not Implemented',
], JSON_INVALID_UTF8_IGNORE | JSON_THROW_ON_ERROR);
