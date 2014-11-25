<?php
/**
*	Decena Php Framework.
*
*	@author		Edgard Decena - edecena@gmail.com
* 	@link		http://www.gnusistemas.com
* 	@version	1.0.0
* 	@package	DecPHP
*	@license 	http://opensource.org/licenses/gpl-license.php GNU Public License
*/

abstract class Cookie extends DecPHP
{
	private function __construct()
	{

	}

	/**
	 * Setea un Cookie en el cliente.
	 * @param string $cookie  Nombre de la Cookie.
	 * @param mixed $valor    Valor de la Cookie.
	 * @param integer $expira Duraciín de la Cookie en minutos.
	 * @param string  $ruta   Ruta en el cliente donde se guardará la Cookie.
	 * @param string $dominio Dominio del server.
	 */
	public static function set($cookie = FALSE, $valor = FALSE, $expira = 1, $ruta = '/', $dominio = FALSE)
	{
		if ($cookie)
		{
			if ($valor)
			{
				$respuesta = FALSE;

				if (!headers_sent())
			    {
			    	if ($dominio == FALSE)
					{
						$dominio = $_SERVER['HTTP_HOST'];
					}

					if (is_numeric($expira))
					{
			        	$expira = $expira * 60 + time();
					}
					else
					{
			        	App::error('El parámetro $expira en Cookie::set($cookie, $valor, $expira) debe ser un número.');
					}

					$respuesta = @setcookie(Hash::get($cookie), Crypt::encrypt($valor), $expira, $ruta, $dominio);

					if ($respuesta)
					{
						$_COOKIE[Hash::get($cookie)] = Crypt::encrypt($valor);
					}
				}
			}
			else
			{
				App::error('Debe proveer un valor $valor a Cookie::set($cookie, $valor).');
			}
		}
		else
		{
			App::error('Debe proveer un nombre de $cookie a Cookie::set($cookie, $valor).');
		}
	}

	/**
	 * Devuelve una Cookie si esta está presente, NULL en caso contrario.
	 * @param  string $cookie Nombre de la Cookie.
	 * @return mixed          Valor de la Cookie si esta existe o NULL en caso contrario.
	 */
	public static function get($cookie = NULL)
	{
		if ($cookie)
		{
			return isset($_COOKIE[Hash::get($cookie)]) ? Crypt::decrypt($_COOKIE[Hash::get($cookie)]) : NULL;
		}
		else
		{
			App::error('Debe proveer un parámetro $cookie a Cookie::get($cookie).');
		}
	}

	/**
	 * Borra una Cookie.
	 * @param  string $cookie   Nombre de la Cookie.
	 * @param  string  $ruta    Ruta en el cliente donde se guardará la Cookie.
	 * @param  string $dominio  Dominio del server donde se alojará la Cookie.
	 * @return boolean          Devuelve TRUE si se eliminó la Cookie y FALSE en caso contrario.
	 */
	public static function del($cookie = FALSE, $ruta = '/', $dominio = FALSE)
	{
		if ($cookie)
		{
			$respuesta = FALSE;

			if (!headers_sent())
			{
				if ($dominio == FALSE)
				{
					$dominio = $_SERVER['HTTP_HOST'];
				}

				$respuesta = setcookie(Hash::get($cookie), '', time() - 3600, $ruta, $dominio);

				if ($respuesta)
				{
					unset($_COOKIE[Hash::get($cookie)]);
				}
			}

			return $respuesta;
		}
		else
		{
			App::error('Debe proveer un nombre de $cookie a Cookie::del($cookie).');
		}
	}

}