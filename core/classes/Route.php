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

abstract class Route extends DecPHP
{
	/**
	 * Bandera que determina si ha habido match en el patrón en App::get() o App::post().
	 * @var boolean
	 */
	private static $_matchPatron = FALSE;

	/**
	 * Determina si se ha hecho match con un $patron de URL en una petición GET o POST.
	 * @param  string   $patron  Patrón de petición de URL.
	 * @param  callable $funcion Función a ser ejecutada en caso de que haga match con el patrón.
	 * @return mixed             Se ejecuta la función $funcion.
	 */
	private static function _matchMetodoPatron($patron = NULL, callable $funcion)
	{
		$patron = explode('/', $patron);
			
		$url    = array();

		foreach ($patron as $i => $parte)
		{
			if (isset($parte[0]) and $parte[0] === ':')
			{
				$url[$i] = '(\w+)';
			}
			else
			{
				$url[$i] = $patron[$i];
			}
		}

		$patron  = implode('/', $url);
		
		$patron  = '/^'.str_replace('/', '\/', $patron).'$/';
		
		$url_get = isset($_GET['url']) ? rtrim('/'.$_GET['url'], '/') : '/';

		if (preg_match($patron, $url_get, $parametros))
		{
            array_shift($parametros);

            self::$_matchPatron = TRUE;

            return call_user_func_array($funcion, array_values($parametros));
        }
	}

	/**
	 * Rutea peticiones GET.
	 * @param  string   $patron  ruta URL.
	 * @param  callable $funcion función a ejecutar si se hace match con el patrón.
	 */
	public static function get($patron = NULL, callable $funcion)
	{
		if (!self::$_matchPatron)
		{
			if (isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD'] === 'GET')
			{
				if ($patron)
				{
					self::_matchMetodoPatron($patron, $funcion);		
				}
				else
				{
					App::error('Debe proveer un parámetro $patron a Route::get($patron, $funcion).');	
				}
			}
		}
	}

	/**
	 * Rutea peticiones POST.
	 * @param  string   $patron  ruta URL.
	 * @param  callable $funcion función a ejecutar si se hace match con el patrón.
	 */
	public static function post($patron = NULL, callable $funcion)
	{
		if (!self::$_matchPatron)
		{
			if (isset($_SERVER['REQUEST_METHOD']) and $_SERVER['REQUEST_METHOD'] === 'POST')
			{
				if ($patron)
				{
					self::_matchMetodoPatron($patron, $funcion);		
				}
				else
				{
					App::error('Debe proveer un parámetro $patron a Route::post($patron, $funcion).');
				}
			}
		}
	}

	/**
	 * Función a ejecutar si no se hace match GET o POST.
	 * @param  callable $funcion función a ejecutar.
	 * @return callable ejecuta la función si no se hace match GET o POST.
	 */
	public static function defaultRoute(callable $funcion)
	{
		if (!self::$_matchPatron)
		{
			self::$_matchPatron = TRUE;
			
			return call_user_func($funcion);
		}
	}

	/**
	 * Redirecciona la apliación vía GET o POST.
	 * @param  string $url    url a ser direccionada la App.
	 * @param  string $method Si la redirección es por GET o por POST.
	 * @param  array  $args   Si es por POST, pueden enviarse datos por $args.
	 */
	public static function redirect($url = NULL, $method = 'GET', $args = array())
	{
		if ($method === 'GET')
		{
			header('location: '.App::url(strtolower($url)));
		}
		elseif ($method === 'POST')
		{
			$ch = curl_init(App::url($url));
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
			curl_exec($ch);
			curl_close($ch);
		}
		else
		{
			App::error('Debe proveer un parámetro $method GET o POST a Route::redirect($url, $method, $args).');
		}
	}

}