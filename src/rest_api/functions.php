<?php
namespace Rest_api;

function json_input_parse($input, array $required_fields, array $optional_fields=[])
{
	$in_object=json_decode($input);
	if(!$in_object) throw new Api_exception("Invalid data", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST, "Input string was not json: ".$input);

	$result=[];
	foreach($required_fields as $key => $field)
	{
		if(property_exists($in_object, $field) && $in_object->$field!=null) $result[$field]=$in_object->$field;
		else throw new Api_exception($field." missing", \Rest_api\Definitions::STATUS_CODE_BAD_REQUEST, "A required field (".$field.") is missing or has no value");
	}

	foreach($optional_fields as $key => $field)
	{
		$result[$field]=property_exists($in_object, $field) ? $in_object->$field : null;
	}

	return $result;
}
