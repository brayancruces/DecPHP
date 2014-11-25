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

abstract class Session extends DecPHP
{

	/**
	 * Inicia una sesión de usuario.
	 */
	public static function begin()
	{
		session_regenerate_id();
		session_start();
	}

	/**
	 * Setea una variable de session.
	 * @param  string $clave variable a ser seteada.
	 * @param  mixed $valor  valor de la variable a ser seteada.
	 */
	public static function setVar($clave = NULL, $valor = NULL)
	{
		if ($clave)
		{
			$_SESSION[$clave] = $valor;
		}
		else
		{
			App::error('Debe proveer una $clave a Session::setVar().');
		}
	}

	/**
	 * Retorna una variable de session.
	 * @param  string $clave variable a ser retornada.
	 * @return mixed         Retorna el valor de la variable de session o NULL en caso de no existir la variable.
	 */
	public static function getVar($clave = NULL)
	{
		if ($clave)
		{
			if (isset($_SESSION[$clave]))
			{
				return $_SESSION[$clave];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			App::error('Debe proveer una $clave a Session::getVar().');
		}
	}

	/**
	 * Autentica en tiempo una session de usuario.
	 */
	public static function autenticate()
	{
		self::setVar('session_time', time());
	}

	/**
	 * Devuelve TRUE si el usuario está autenticado, FALSE en caso contrario.
	 */
	public static function autenticated()
	{
		self::timeCompleted();

		$varSession = self::getVar('session_time');

		return isset($varSession) ? TRUE : FALSE;
	}

	/**
	 * Destruye las variables (string) de session que se le pasen por parámetros.
	 */
	public static function unsetVar()
	{
		foreach (func_get_args() as $clave)
		{
			unset($_SESSION[$clave]);
		}
	}

	/**
	 * Valida si el tiempo de session ha sido alcanzado.
	 * @return TRUE / FALSE En caso de haberse completado el tiempo de session retorna TRUE, FALSE en otro caso.
	 */
	public static function timeCompleted()
	{
		$config_session_time = App::getConfig('session_time');

		if (!isset($config_session_time) OR App::getConfig('session_time') === 0)
		{
			return FALSE;
		}

		$session_time = self::getVar('session_time');

		if (isset($session_time))
		{
			if (time() - $session_time > App::getConfig('session_time') * 60)
			{
				self::end();
				return TRUE;
			}
			else
			{
				self::setVar('session_time', time());
			}
		}
		else
		{
			return TRUE;
		}
	}

	/**
	 * Termina la session al destruir todas las variables de session.
	 */
	public static function end()
	{
		session_destroy();
		unset($_SESSION);	
	}

}