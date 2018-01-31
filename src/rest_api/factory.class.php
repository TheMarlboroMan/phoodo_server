<?php
namespace Rest_api;

class Factory
{
	public function get_resource($request_type, Config $config)
	{
		//Autoload...
		$path=$config->relative_path.$config->file_prefix.strtolower($request_type).$config->file_extension;
		if(!file_exists($path))
		{
			throw new Api_exception("Api resource does not exist", Definitions::STATUS_CODE_RESOURCE_NOT_FOUND, "The path ".$path." does not refer to a valid file");
		}

		require_once($path);

		$classname=$config->class_namespace.$config->classname_prefix.$request_type;

		if(!class_exists($classname))
		{
			throw new Api_exception("Api resource does not exist", Definitions::STATUS_CODE_RESOURCE_NOT_FOUND, "Class ".$classname." was not found in ".$path);
		}

		return new $classname;
	}
};
