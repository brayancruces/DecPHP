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

abstract class Input extends DecPHP
{
	private function __construct()
	{

	}

	/**
	 * Devuelve una variable GET si está definida o el arreglo completo $_GET.
	 * @param  string $clave nombre de la variable GET.
	 * @return string        devuelve valor de $_GET[$clave]
	 */
	public static function get($clave = NULL)
	{
		if ($clave)
		{
			return isset($_GET[$clave]) ? $_GET[$clave] : NULL;
		}
		else
		{
			return isset($_GET) ? $_GET : NULL;
		}
	}

	/**
	 * Devuelve una variable POST si está definida o el arreglo completo $_POST.
	 * @param  string $clave nombre de la variable POST.
	 * @return string        devuelve valor de $_POST[$clave]
	 */
	public static function post($clave = NULL)
	{
		if ($clave)
		{
			return isset($_POST[$clave]) ? $_POST[$clave] : NULL;
		}
		else
		{
			return isset($_POST) ? $_POST : NULL;
		}
	}

}