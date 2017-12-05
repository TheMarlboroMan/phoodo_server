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
		http_response_code($this->code);
		header('Content-type: '.$this->content_type.'; charset=UTF-8;');
		header('Access-Control-Allow-Origin: *');
		die($this->body);
	}

	public static function 	get_error_response(\Exception $e)
	{
		$response=['error_description' => $e->getMessage(), 'http_status_code' => $e->getCode()];
		return new Response(json_encode($response), $e->getCode(), Definitions::TYPE_JSON);
	}
}
?>
