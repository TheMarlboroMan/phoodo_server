<?php
namespace Phoodo;

/*function extract_valid_token_from_headers(\Rest_api\Request_headers $headers)
{
	//TODO: Create a function in the Phoodo namespace that does it all: extract from headers, get from database and return. Throw when fails.
}
*/

function get_and_refresh_token_from_headers(\Rest_api\Request_headers $headers)
{
	if(!$headers->exists("token"))
	{
		throw new App_exception("token not present or malformed", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
	}

	$token=User_token::get_current_by_token($headers->get("token")[0]);
	if(!$token)
	{
		throw new App_exception("Invalid or expired token", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
	}

	$token->refresh();
	return $token;
}

function get_verified_user_from_token(User_token $token)
{
	$user=null;
	try
	{
		$user=new User($token->get_user_id()); //Might return an empty user, actually... 
	}
	catch(\Exception $e)
	{
		throw new App_exception("Token does not represent an user", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
	}
	
	if(!$user->is_verified())
	{
		throw new App_exception("Unverified user", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST);
	}

	return $user;
}
