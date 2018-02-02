<?php
namespace Phoodo;

class Email extends \Email_base
{
	private $html_title=null;
	private $html_header=null;
	private $html_highlight=null;
	private $html_text=null;
	private $built=false;

	public function __construct() {
		parent::__construct();
		$this->establecer_smtp_servidor(\Constantes_app_phoodo::EMAIL_SERVER);
		$this->establecer_smtp_usuario(\Constantes_app_phoodo::EMAIL_USER);
		$this->establecer_smtp_pass(\Constantes_app_phoodo::EMAIL_PASS);
	}

	public function build_body() 
	{	
		$body_width=900;
		$highlight=null;

		$build_highlight=function() {
			return $this->html_highlight ? <<<HL
		<div style="margin:2em 1em ; padding: 2em 10px; background-color: #DDDDDD; font-weight: bold">
			{$this->html_highlight}
		</div>
HL
			: null;
		};

		$this->establecer_html_cuerpo(<<<MAQUETACION
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="es" lang="es">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>{$this->html_title}</title>
</head>
<body style="background: #fff; font-family: Helvetica, monospace; font-size: 1.2em;">
	<div style="margin:20px auto; width:{$body_width}px; background-color: #FCF9EA; border: 1px #ccc solid;">
		<div style="padding: 1em; background-color: #AFDD92; font-size: 2em; border-bottom: 4px #C8EA59 solid;">
			{$this->html_header}
		</div>

		{$build_highlight()}

		<div style="margin:2em 1em; padding: 2em 10px; background-color: #F5F1DC;">
			{$this->html_text}
		</div>
		<div style="text-align: center; padding: 10px; background: #AFDD92; border-top: 4px #C8EA59 solid;">
		Phoodo 2018, food for the busy people!.
		</div>
	</div>
</body>
</html>
MAQUETACION
);

		$this->built=true;
	}

	public function get_view() {
		return $this->get_html_cuerpo();
	}

/*
	public function test_adjunto()
	{
		$this->html_title='TITLE HTML';
		$this->html_header='HEADER HTML';
		$this->html_highlight='destacado';
		$this->html_text='texto texto texto...';
		$this->establecer_texto_plano("ESTE ES EL TEXTO PLANO");
		$this->establecer_origen('no-reply', 'dominio');
		$this->establecer_asunto('TEST ASUNTO');
		$this->establecer_destinatario('email@email.com');

		$this->adjuntar_archivo_por_ruta('test_2.txt');
		$this->adjuntar_archivo_por_ruta('test.txt');

		$this->CUERPO_HTML();
		$this->enviar();
	}

*/
	public function build_validation_email(User $user){
		$verification_url=\Constantes_app_phoodo::URL_WEB.'validar-cuenta/'.$user->get_verification_code().'/'.$user->get_email();

		$this->html_title='Verifica tu cuenta de Phoodo';
		$this->html_header='Verifica tu cuenta de Phoodo';
		$this->html_highlight='Para usar Phoodo necesitas activar tu cuenta.';


//TODO: What if you haven't asked for an account???
//TODO: How long is the link valid???

		$this->html_text=<<<T
<p>Para activar tu cuenta de Phoodo visita el siguiente enlace o pégalo en la barra de tu navegador:</p>

<a style="text-decoration: none;" href="{$verification_url}">{$verification_url}</a>

<p>¡Te esperamos!</p>
T;

		$this->establecer_texto_plano("Necesitas un lector de correo válido");
		$this->establecer_origen('dani', 'caballorenoir.net');
		$this->establecer_asunto('Phoodo - Verifica tu cuenta');
		$this->establecer_destinatario($user->get_email());

		$this->build_body();
	}

	public function build_welcome_email(User $user) {

		$this->html_title='¡Bienvenid@ de Phoodo!';
		$this->html_header='¡Bienvenid@ de Phoodo!';
		$this->html_highlight='Te damos la bienvenida a Phoodo!';

		$this->html_text=<<<T
<p>Esperamos que Phoodo te ayude a organizar tus comidas y compras. ¿Qué puedes hacer ahora?</p>

<ul>
	<li>Haz login en la aplicación con tu email y contraseña.</li>
	<li>Crea tus propias categorías de alimentación.</li>
	<li>Crea tus propias recetas.</li>
	<li>Organiza tu semana y genera listas de la compra.</li>
</ul>

<p>¡Te esperamos dentro!</p>
T;

		$this->establecer_texto_plano("Necesitas un lector de correo válido");
		$this->establecer_origen('dani', 'caballorenoir.net');
		$this->establecer_asunto('Phoodo - ¡Bienvenid@!');
		$this->establecer_destinatario($user->get_email());

		$this->build_body();
	}	

	public function build_test($mail)
	{
		$this->html_title='HTML title of the test!';
		$this->html_header='You have been selected to get an email!';
//		$this->html_highlight='This free email has been delivered to you!';
		$this->html_text='Enjoy your free test!';

		$this->establecer_texto_plano("You need a proper email reader man!");
		$this->establecer_origen('dani', 'caballorenoir.net');
		$this->establecer_asunto('Phoodo - This is a test!');
		$this->establecer_destinatario($mail);

		$this->build_body();
	}

	public function send() {
		if(!$this->built) {
			throw new \Exception("Email must be built before sent!");
		}

		$this->enviar();
	}
}
