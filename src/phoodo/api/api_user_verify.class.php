<?php
namespace Phoodo;

class Api_user_verify extends \Rest_api\Resource implements \Rest_api\Api_post
{
	public function post($_input, \Rest_api\Request_headers $headers, array $get)
	{
		database_connect();

		$input=\Rest_api\json_input_parse($_input, ['email', 'verification_code']);

		if(!$input['email'] || !$input['verification_code'])
		{
			throw new App_exception("Invalid or missing input", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		$user=User::get_by_email($input['email']);
		if(!$user)
		{
			throw new App_exception("Invalid user", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		if($user->is_verified())
		{
			throw new App_exception("User already verified", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		if(!$user->check_verification_code($input['verification_code']))
		{
			throw new App_exception("Invalid verification code", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		$user->verify();

		$email=new Email();
		$email->build_welcome_email($user);
		$email->send();

		return new \Rest_api\Response(json_encode(["result" => 1]), \Rest_api\Definitions::STATUS_CODE_OK);
	}
};
