<?php

namespace Phoodo;

class User extends \Contenido_bbdd
{
	const TABLA='phoodo_user';
	const ID='user_id';

	public function NOMBRE_CLASE() {return "Phoodo\User";}
	public function TABLA() {return self::TABLA;}
	public function ID() {return self::ID;}

	private static $dictionary=array(
		'user_id' => 'user_id',
		'created_on' => 'created_on',
		'email' => 'email',
		'pass' => 'pass',
		'verification_code' => 'verification_code',
		'verified' => 'verified'
	);

	protected $user_id=null;
	protected $created_on=null;
	protected $email=null;
	protected $pass=null;
	protected $verification_code=null;
	protected $verified=null;

	public function	get_user_id() {return (int)$this->user_id;}
	public function	get_email() {return $this->email;}
	public function	get_verification_code() {return $this->verification_code;} 
	public function	is_verified() {return $this->verified;}

	public function __construct(&$data=null)
	{
		parent::__construct($data, self::$dictionary, self::TABLA, self::ID);
	}

	public function create(&$input)
	{
		$input['verified']=false;
		$input['pass']=md5($input['pass']);
		$input['verification_code']=md5(str_shuffle($input['email'].$input['pass'].date("Ymd")));
		return parent::base_crear($input, 'created_on', 'NOW()');
	}

	public function update(&$input=null)
	{	
		if(!$input) throw new \Exception("Unable to update database entity without input");
		if(isset($input['pass'])) $input['pass']=md5($input['pass']);

		return parent::base_modificar($input);
	}

	public function delete(&$data=null)
	{
		return parent::base_eliminar_fisico($data);
	}

	//Services
	public function verify()
	{
		$data=['verified' => true];
		return $this->update($data);
	}

	public function check_verification_code($code)
	{
		return $this->verification_code==$code;
	}

	//Pass must be sent to this function without hashing.
	public function check_pass($pass)
	{
		return $this->pass==md5($pass);
	}

	public static function get_by_email($email)
	{
		$sql=new User_sql();
		$dummy=new User();
		return $dummy->obtener_objeto_por_texto($sql->get_by_email($email));
	}

	public static function get_public_data(User $user)
	{
		return ['user_id' => $user->user_id,
			'email' => $user->email,
			'created_on' => $user->created_on];
	}
};
