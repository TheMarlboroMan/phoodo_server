<?php
namespace Phoodo;

class App_exception extends \Exception
{
	public function __construct($message, $code, Exception $previous = null) 
	{
		parent::__construct($message, $code, $previous);
	}
};
