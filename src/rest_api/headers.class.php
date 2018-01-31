<?php
namespace Rest_api;

class Request_headers
{
	private $headers=[];

	public function __construct()
	{
		foreach(apache_request_headers() as $key => $value)
		{
			//Take care of multiple values...
			$vals=explode(',', $value);
			foreach($vals as $vk => &$vv) $vv=trim($vv); 
			$this->headers[$key]=$vals;
		}
	}

	//Returns an array, as it may have multiple values separated by a comma... Or null, if nothing exists.
	public function get($key)
	{
		if(array_key_exists($key, $this->headers)) return $this->headers[$key];
		else return null;
	}

	public function exists($key)
	{
		return array_key_exists($key, $this->headers);
	}
};
