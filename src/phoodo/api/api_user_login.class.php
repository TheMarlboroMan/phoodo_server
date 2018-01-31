<?php
namespace Phoodo;

class Api_user_login extends \Rest_api\Resource implements \Rest_api\Api_post
{
	public function post($_input, \Rest_api\Request_headers $headers, array $get)
	{
		$input=\Rest_api\json_input_parse($_input, ['email', 'pass']);

		$user=User::get_by_email($input['email']);
		if(!$user)
		{
			throw new App_exception("Invalid user", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		if(!$user->is_verified())
		{
			throw new App_exception("Unverified user", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		if(!$user->check_pass($input['pass']))
		{
			throw new App_exception("Invalid user-pass pair", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
		}

		$token=new User_token();
		$create_data=['user_id' => $user->get_user_id()];
		$token->create($create_data);

		$result=['token' => $token->get_token(), 'user_id' => $user->get_user_id()];
		return new \Rest_api\Response(json_encode($result), \Rest_api\Definitions::STATUS_CODE_OK);
	}
};
