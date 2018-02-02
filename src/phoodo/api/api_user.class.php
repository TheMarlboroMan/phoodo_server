<?php
namespace Phoodo;

class Api_user extends \Rest_api\Resource implements \Rest_api\Api_post, \Rest_api\Api_delete, \Rest_api\Api_get, \Rest_api\Api_put
{
	public function post($_input, \Rest_api\Request_headers $headers, array $get)
	{
		database_connect();

		$input=\Rest_api\json_input_parse($_input, ['email', 'pass']);

		if(User::get_by_email($input['email']))
		{
			throw new App_exception("email in use", \Rest_api\Definitions::STATUS_CODE_CONFLICT);
		}

		$user=new User();
		$user->create($input);

		$email=new Email();
		$email->build_validation_email($user);
		$email->send();

		return new \Rest_api\Response(json_encode(["user_id" => $user->get_user_id()]), \Rest_api\Definitions::STATUS_CODE_OK);
	}	

	public function delete($_input, \Rest_api\Request_headers $headers, array $get)
	{
		database_connect();

		$token=get_and_refresh_token_from_headers($headers);
		$user=get_verified_user_from_token($token);
		$user->delete();
		User_token::delete_by_user($user);
		//TODO: What about the rest of user data??

		return new \Rest_api\Response(json_encode(["result" => 1]), \Rest_api\Definitions::STATUS_CODE_OK);
	}

	public function get($_input, \Rest_api\Request_headers $headers, array $get)
	{
		database_connect();

		$token=get_and_refresh_token_from_headers($headers);
		$user=get_verified_user_from_token($token);
		return new \Rest_api\Response(json_encode(User::get_public_data($user)), \Rest_api\Definitions::STATUS_CODE_OK);
	}

	public function put($_input, \Rest_api\Request_headers $headers, array $get)
	{
		database_connect();

		$input=\Rest_api\json_input_parse($_input, ['pass'] /*[]*/); //There are no optional parameters.

		$token=get_and_refresh_token_from_headers($headers);
		$user=get_verified_user_from_token($token);
		$user->update($input);

		return new \Rest_api\Response(json_encode(["result" => 1]), \Rest_api\Definitions::STATUS_CODE_OK);
	}
};
