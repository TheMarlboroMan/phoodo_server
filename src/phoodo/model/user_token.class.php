<?php

namespace Phoodo;

class User_token extends \Contenido_bbdd
{
	const TABLE='phoodo_user_token';
	const ID='token';

	public function NOMBRE_CLASE() {return "Phoodo\User_token";}
	public function TABLA() {return self::TABLE;}
	public function ID() {return self::ID;}

	private static $dictionary=array(
		'token' => 'token',
		'created_on' => 'created_on',
		'valid_until' => 'valid_until',
		'user_id' => 'user_id'
	);

	protected	$token=null;
	protected	$created_on=null;
	protected	$valid_until=null;
	protected	$user_id=null;

	public function get_token() {return $this->token;}
	public function get_user_id() {return $this->user_id;}

	public function __construct(&$data=null)
	{
		parent::__construct($data, self::$dictionary, self::TABLE, self::ID);
	}

	public function create(&$input)
	{
		$token_base=date('Ymdhis').str_shuffle(".-*").$this->user_id.microtime();
		$input['token']=md5($token_base);
		return parent::base_crear($input, 'created_on, valid_until', 'NOW(), NOW()+INTERVAL 30 MINUTE');
	}

	public function update(&$input=null)
	{	
		if(!$input) throw new \Exception("Unable to update database entity without input");
		return parent::base_modificar($input);
	}

	public function delete(&$data=null)
	{
		return parent::base_eliminar_fisico($data);
	}

	//Services
	public function refresh()
	{
		$sql=new User_token_sql();
		$query=new \Consulta_mysql();
		$query->consultar($sql->refresh($this->token));
	}

	public static function get_current_by_token($token)
	{
		$sql=new User_token_sql();
		$dummy=new User_token();
		return $dummy->obtener_objeto_por_texto($sql->get_current_by_token($token));
	}

	public static function delete_by_user(User $user)
	{
		$sql=new User_token_sql();
		$query=new \Consulta_mysql();
		$query->consultar($sql->delete_by_user($user->get_user_id()));
	}
};
