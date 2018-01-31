<?php
namespace Rest_api;

//Specific exception to troubleshoot internals.

class Api_exception extends \Exception
{
	private	$log_info=null;

	public function	get_log_info() {return $this->log_info;}

	public function __construct($message, $code, $_li, Exception $previous = null) 
	{
		parent::__construct($message, $code, $previous);
		$this->log_info=$_li;
	}
};
