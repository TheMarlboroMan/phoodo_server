<?php

namespace Phoodo;

class User_sql extends \Base_textos_sql
{
	public function TABLA() {return User::TABLA;}
	public function ORDEN_DEFECTO() {return "email ASC";}
	public function CRITERIO_DEFECTO() {return 'AND TRUE';}
	public function VER_TODO() {return 'TRUE';}
	public function VER_VISIBLE() {return 'TRUE';}
	public function VER_PUBLICO() {return 'TRUE';}

	public function TEXTOS_CREAR_TABLAS()
	{	
		$TABLE=$this->TABLA();
		$result=[
			"DROP TABLE IF EXISTS ".$TABLE.";",
			"CREATE TABLE ".$TABLE."
(
	user_id		INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
	created_on	DATETIME NOT NULL,
	email		VARCHAR(100) NOT NULL,
	pass		VARCHAR(32) NOT NULL,
	verification_code 	VARCHAR(32) NOT NULL,
	verified	BOOLEAN NOT NULL DEFAULT FALSE
);",
			"ALTER TABLE ".$TABLE." ADD UNIQUE (email);"
		];

		return $result;
	}

	public function get_by_email($email)
	{
		return $this->obtener_publico("AND email='".$email."'");
	}
};
