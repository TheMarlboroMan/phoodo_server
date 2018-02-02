<?php
ini_set('display_errors', 0);

//TODO: Careful where we move this, with namespace names...
function do_log($text) {

	//TODO: This needs to be configured....
	$file=fopen("/opt/lampp/htdocs/phoodo/log/phoodo.log", "a");
	fwrite($file, "[".date("y-m-d h:i:s")."] : ".$text."\n");
	fclose($file);
}

set_error_handler(function ($err_severity, $err_msg, $err_file, $err_line, array $err_context) {
	if($err_severity!==E_DEPRECATED) 
		throw new Exception("Api error handler (".$err_severity."): ".$err_msg.' ['.$err_file.':'.$err_line.']', $err_severity);
});

register_shutdown_function(function () {
	$error=error_get_last();
	if($error['type']===E_ERROR) {
		//TODO: 500??... As in a magic number?
		http_response_code(500);
		$msg='Something terrible happened in '.$error['file'].' '.$error['line'].' : '.$error['message'];
		do_log($msg);
		die();
	}
});

//TODO: This is supposed to be user-dependant.
$ex_handler=function($e) {
	switch(get_class($e))
	{
		case 'Phoodo\App_exception':
			do_log($e->getMessage()." ".$e->getCode()."\nTHAT WAS AN APP_EXCEPTION. THIS IS api.php FILE\n");
			throw new \Exception($e->getMessage(), $e->getCode());
		break;
	}
};

require_once("src/rest_api/require.php");

$api_response=null;
$api_factory=new \Rest_api\Factory();

$request_type=isset($_GET['type']) ? strtolower($_GET['type']) : null;
$request_method=strtolower($_SERVER['REQUEST_METHOD']);

try{
	//TODO: Check content-type of request input???
	$request_input=file_get_contents("php://input");

	//TODO: Better to get the raw query string?.
	$request_get=$_GET;
	$request_headers=new\Rest_api\Request_headers();

	//TODO: LOG EVERYTHING, INCLUDING GET AND HEADERS...!!!!
	do_log("requesting ".$request_method.":".$_SERVER['REQUEST_URI']." with [".$request_input."]");

	$config=new \Rest_api\Config("src/phoodo/api/", "\\Phoodo\\");
	$api_resource=$api_factory->get_resource($request_type, $config);

	//Load the main engine.
	require_once("renoir_init.php");

	//Load application specific functions and modules.
	require_once("src/phoodo/autoload.php");

	//TODO: Load when required..
	require_once("src/phoodo/model/user.class.php");
	require_once("src/phoodo/model/user.sql.php");
	require_once("src/phoodo/model/user_token.class.php");
	require_once("src/phoodo/model/user_token.sql.php");

	$dispatcher=new \Rest_api\Dispatcher($ex_handler);
	$dispatcher->dispatch($request_method, $api_resource, $request_input, $request_headers, $request_get)->resolve_response();
}
catch(\Exception $e){
	\Rest_api\Response::get_error_response($e)->resolve_response();
}

//curl -X POST|GET|PUT|DELETE "http://localhost/phoodo/api/shit?hola=1&cosa=2" -H "Content-type: text/plain" -d "Hello fuckers"
