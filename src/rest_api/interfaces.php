<?php
namespace Rest_api;

//This is actually unnecessary and misused... These interfaces are not referred
//to anywhere else in the code except in "implements" statements, thus do not
//actually force client code to commit to them. 
//There is NO NEED to catch inside these.

interface Api_post
{
	public function post($input, Request_headers $headers, array $get);
};

interface Api_get
{
	public function get($input, Request_headers $headers, array $get);
};

interface Api_put
{
	public function put($input, Request_headers $headers, array $get);
};

interface Api_delete
{
	public function delete($input, Request_headers $headers, array $get);
};
