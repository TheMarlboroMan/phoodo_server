<?php
namespace Rest_api;

class Response
{
	public function __construct($_b, $_c, $_ct=Definitions::TYPE_JSON)
	{
		$this->body=$_b;
		$this->code=$_c;
		$this->content_type=$_ct;
	}

	public	$body;
	public	$code;
	public	$content_type;

	public function	resolve_response()
	{
		//TODO... Actually, this is not good... This should be configurable.
		header('Access-Control-Allow-Origin: *');
		header('Content-type: '.$this->content_type.'; charset=UTF-8;');
		http_response_code($this->code);
		die($this->body);
	}

	public static function 	get_error_response(\Exception $e)
	{
		$response=['error_description' => $e->getMessage(), 'http_status_code' => $e->getCode()];
		return new Response(json_encode($response), $e->getCode(), Definitions::TYPE_JSON);
	}
};

class CorsResponse extends Response {

	public function __construct($_b=null, $_c=null, $_ct=Definitions::TYPE_JSON) {
		parent::__construct($_b, $_c, $_ct);
	}

	public function resolve_response() {
		header('Allow: OPTIONS, GET, PUT, POST, DELETE');
		header('Access-Control-Allow-Headers: Content-Type');
		parent::resolve_response();
	}
};
