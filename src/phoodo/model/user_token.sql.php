<?php

namespace Phoodo;

class User_token_sql extends \Base_textos_sql
{
	public function TABLA() {return User_token::TABLE;}
	public function ORDEN_DEFECTO() {return "created_on ASC";}
	public function CRITERIO_DEFECTO() {return 'AND TRUE';}
	public function VER_TODO() {return 'TRUE';}
	public function VER_VISIBLE() {return 'TRUE';}
	public function VER_PUBLICO() {return 'TRUE';}

	public function TEXTOS_CREAR_TABLAS()
	{	
		$TABLE=$this->TABLA();
		$result=[
			"DROP TABLE IF EXISTS ".$TABLE,
			"CREATE TABLE ".$TABLE."
(
	token		CHAR(32) NOT NULL PRIMARY KEY,
	created_on	DATETIME NOT NULL,
	valid_until	DATETIME NOT NULL,
	user_id		INT UNSIGNED NOT NULL
);",
			"ALTER TABLE ".$TABLE." ADD INDEX (token)"
		];

		return $result;
	}

	public function get_current_by_token($token)
	{
		return $this->obtener_publico("AND token='".$token."'
AND valid_until >= NOW()");
	}

	public function delete_by_user($id)
	{
		return "DELETE FROM ".$this->TABLA()." 
WHERE user_id='".$id."'";
	}

	public function refresh($token)
	{
		return "UPDATE ".$this->TABLA()."
SET valid_until=NOW()+INTERVAL 30 MINUTE
WHERE token='".$token."'";
	}
};
