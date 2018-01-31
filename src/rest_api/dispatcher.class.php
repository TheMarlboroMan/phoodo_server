<?php
namespace Rest_api;

class Dispatcher
{
	private $custom_exception_handler=null;

	public function __construct($_ch=null)
	{
		$this->custom_exception_handler=$_ch;
//TODO: Actually, this MUST be a closure.
		if($this->custom_exception_handler != null && !is_callable($this->custom_exception_handler))
		{
			throw new Api_exception("Custom exception handler for dispacher is not callable", Definitions::STATUS_CODE_INTERNAL_SERVER_ERROR, "Api is misconfigured");
		}
	}

	public function dispatch($request_method, Resource $api_resource, $request_input, Request_headers $request_headers, array $request_get)
	{
		try
		{
			$interface=null;

			switch($request_method)
			{
				case 'post': 
				case 'get':
				case 'put':
				case 'delete':
					$interface="\\Rest_api\\Api_".$request_method;
					if(!$api_resource instanceof $interface)
					{
						throw new Api_exception("Resource method ".$request_method." does not exist", Definitions::STATUS_CODE_METHOD_NOT_ALLOWED, "The resource does not implement the interface");
					}

					$result=$api_resource->$request_method($request_input, $request_headers, $request_get);
					if(!$result instanceof Response) throw new Api_exception("Illegal resource return type", Definitions::STATUS_CODE_INTERNAL_SERVER_ERROR, "The resource does not return the proper type");
					return $result;
				break;
				case 'options':
					//TODO: This should be configurable in an application basis, so we should pass some params.
					return new CorsResponse();
				break;
				default:
					throw new Api_exception("Unsupported http verb", Definitions::STATUS_CODE_METHOD_NOT_ALLOWED, "The requested verb '".$request_method."' is not supported by the api");
				break;
			}
		}
		catch(\Excepcion_consulta_mysql $e) {
			do_log($e->getMessage()."\nTHAT WAS A MYSQL ERROR. THIS IS dispatcher FILE\n");
			throw new \Exception(\Rest_api\Definitions::MESSAGE_DATABASE_ERROR, \Rest_api\Definitions::STATUS_CODE_INTERNAL_SERVER_ERROR);
		}
		catch(Api_exception $e)	{
			do_log($e->get_log_info()."\nTHAT WAS A API EXCEPTION. THIS IS dispatcher FILE\n");
			throw new \Exception($e->getMessage(), $e->getCode());
		}
		catch(\Exception $e) {
			
			if($this->custom_exception_handler) {
				$this->custom_exception_handler->__invoke($e);
			}

			//This is the last line...
			do_log($e->getMessage()." ".$e->getCode()."\nTHAT WAS A BASIC EXCEPTION. THIS IS dispatcher FILE\n");
			throw new \Exception(\Rest_api\Definitions::MESSAGE_GENERIC_ERROR.$e->getMessage(), \Rest_api\Definitions::STATUS_CODE_INTERNAL_SERVER_ERROR);
		}
	}
};
