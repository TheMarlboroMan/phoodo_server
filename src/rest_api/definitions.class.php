<?php
namespace Rest_api;

class Definitions
{
	const 	STATUS_CODE_OK=200;

	const	STATUS_CODE_BAD_REQUEST=400;
	const	STATUS_CODE_RESOURCE_NOT_FOUND=404;
	const	STATUS_CODE_METHOD_NOT_ALLOWED=405;
	const	STATUS_CODE_CONFLICT=409;

	const	STATUS_CODE_INTERNAL_SERVER_ERROR=500;

	const	MESSAGE_GENERIC_ERROR="Error: ";
	const	MESSAGE_DATABASE_ERROR="Database error: ";
	const	MESSAGE_INVALID_INPUT="Failed input";

	const	TYPE_JSON="text/json";
};
