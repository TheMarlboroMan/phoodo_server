<?php
ini_set('display_errors', 1);

//TODO: Careful where we move this, with namespace names...
function do_log($text)
{
	$file=fopen("log/phoodo.log", "a");
	fwrite($file, "[".date("y-m-d h:i:s")."] : ".$text."\n");
	fclose($file);
}

//TODO: Careful where we move this, with namespace names...
$ex_handler=function($e) {
	switch(get_class($e))
	{
		case 'Phoodo\App_exception':
			do_log($e->getMessage()." ".$e->getCode()."\nTHAT WAS AN APP_EXCEPTION. THIS IS app_functions.php FILE\n");
			throw new \Exception($e->getMessage(), $e->getCode());
		break;
	}
};

require_once("src/rest_api/require.php");

$api_response=null;
$api_factory=new \Rest_api\Factory();

$request_type=isset($_GET['type']) ? strtolower($_GET['type']) : null;
$request_method=strtolower($_SERVER['REQUEST_METHOD']);

try
{
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

	$request_input=file_get_contents("php://input");

	//TODO: Check content-type of request input???

	//TODO: Better to get the raw query string?.
	$request_get=$_GET;
	$request_headers=new\Rest_api\Request_headers();

	$dispatcher=new \Rest_api\Dispatcher($ex_handler);
	$dispatcher->dispatch($request_method, $api_resource, $request_input, $request_headers, $request_get)->resolve_response();
}
catch(\Exception $e)
{
	\Rest_api\Response::get_error_response($e)->resolve_response();
}

//curl -X POST|GET|PUT|DELETE "http://localhost/phoodo/api/shit?hola=1&cosa=2" -H "Content-type: text/plain" -d "Hello fuckers"
