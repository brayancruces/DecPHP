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

abstract class App extends DecPHP
{	
	/**
	 * Arreglo clave-valor que almacena las claves de configuración de la aplicación.
	 * @var array
	 */
	private static $_app_config = array();

	/**
	 * Controlador de la petición GET.
	 * @var string.
	 */
	private static $_controller = NULL;

	/**
	 * Método a ejecutar del controlador.
	 * @var string.
	 */
	private static $_method = NULL;

	/**
	 * Parámetros de la petición..
	 * @var string.
	 */
	private static $_params = array();
	
	/**
	 * Devuelve el controlador de la petición.
	 * @return string controlador de la petición.
	 */
	public static function getController()
	{
		return self::$_controller;
	}

	/**
	 * Devuelve el método a ejecutar en la petición.
	 * @return string método de la petición.
	 */
	public static function getMethod()
	{
		return self::$_method;
	}

	/**
	 * Devuelve los parámetros de la petición
	 * @return array parámetros de la petición.
	 */
	public static function getParams()
	{
		return self::$_params;
	}

	/**
	 * Retorna una url completa valida del proyecto.
	 * @param  string $url url valida.
	 * @return string url completa y valida.
	 */
	public static function url($url = NULL)
	{
		return parent::url($url);
	}

	/**
	 * Setea variables de configuración de la App.
	 * @param string $clave parámetro de configuración.
	 * @param string $valor valor del parámetro de configuración.
	 */
	public static function setConfig($clave = NULL, $valor = NULL)
	{
		if ($clave)
		{
			self::$_app_config[$clave] = $valor;
		}
		else
		{
			self::error('Debe proveer un parámetro $clave a App::setConfig().');
		}
	}

	/**
	 * Devuelve un parámetro de configuración de la aplicación o NULL en caso que no exista.
	 * @param  string $clave parámetro de configuración.
	 * @return parámetro/NULL Retorna el valor de la $clave de configuración, NULL en caso contrario.
	 */
	public static function getConfig($clave = NULL)
	{
		if ($clave)
		{
			if (array_key_exists($clave, self::$_app_config))
			{
				return self::$_app_config[$clave];
			}
			else
			{
				return NULL;
			}
		}
		else
		{
			return self::$_app_config;
		}
	}

	/**
	 * Establece la ruta/directorio para la autocarga de clases de un proyecto.
	 * @param  string $path Ruta o directorio a establecerse para la autocarga de clases.
	 */
	public function classAutoload($path = NULL)
	{	
		spl_autoload_register(function($clase)
		{
			$clase = self::dir(self::getConfig('classes_folder')).$clase.'.php';

			if (is_readable($clase))
			{
				require_once $clase;
			}
			else
			{
				self::error('No se encuentra la clase '.$clase);
			}
		});
	}

	/**
	 * Ejecuta todas las instrucciones dadas en el parámetro $funcion.
	 * @param  callable $funcion función a ser ejecutada.
	 * @return callable  
	 */
	public static function run(callable $funcion)
	{
		if(self::getConfig('debug_mode'))
		{
			ini_set('error_reporting', E_ALL | E_NOTICE | E_STRICT);
			ini_set('trackerrors', 'On');
			ini_set('display_errors', '1');
		}
		else
		{
			ini_set('display_errors', '0');
		}

		setlocale(LC_MONETARY, self::getConfig('set_locale')); // para localeconv()

		setlocale(LC_NUMERIC, self::getConfig('set_locale')); // para localeconv()

		setlocale(LC_TIME, self::getConfig('set_locale')); // formato de fecha y hora con strftime()

		if (self::getConfig('time_zone'))
		{
			date_default_timezone_set(self::getConfig('time_zone')); // Establece zona horaria.
		}

		return call_user_func($funcion);
	}
	
	/**
	 * Llama a un controlador dada una petición GET.
	 * Debe estar habilitado el mod_rewrite del servidor web que habilite la nomenclatura:
	 * /controlador/método/p1/p2... y el .htaccess correspondiente para tal nomenclatura.
	 */
	public static function callController()
	{
		if(isset($_GET['url']))
        {
			$url         = filter_input(INPUT_GET, 'url', FILTER_SANITIZE_URL);
			
			$url         = explode('/', $url);
			
			$url         = array_filter($url);
			
			$controlador = strtolower(array_shift($url));
			
			$metodo      = strtolower(array_shift($url));
			
			$parametros  = $url;
        }
        
        if(!isset($controlador))
        { 
            $controlador = 'default';

            if (!is_readable(self::dir(self::getConfig('controllers_folder')).'defaultController.php'))
            {
            	self::error('Debe implementar el controlador por defecto defaultController.php');
            }
        }

        if(empty($metodo))
        {
            $metodo = 'index';
        }
        
        if(!isset($parametros))
        {
            $parametros = array();
        }

        $rutaControlador = self::dir(self::getConfig('controllers_folder')).$controlador.'Controller.php';

		if (is_readable($rutaControlador))
		{
			require_once $rutaControlador;

			$cont        = $controlador;
			
			$controlador = $controlador.'Controller';
			
			$controlador = new $controlador;

			self::$_controller = $cont;
			self::$_method     = $metodo;
			self::$_params     = $parametros;

			if (!is_callable(array($controlador, $metodo)))
			{
				if (!method_exists($controlador, 'index'))
	        	{
	        		self::error('Debe implementar obligatoriamente un método index en el controlador '.$cont.'Controller.');	
	        	}

				Route::redirect('/'.$cont);	
			}

			if (method_exists($controlador, '__before'))
        	{
        		call_user_func(array($controlador, '__before'));
        	}
			
			if(isset($parametros))
			{
				call_user_func_array(array($controlador, $metodo), $parametros);
	        }
	        else
	        {
	        	call_user_func(array($controlador, $metodo));
	        }

	        if (method_exists($controlador, '__after'))
        	{
        		call_user_func(array($controlador, '__after'));
        	}
		}
		else
		{
			Route::redirect();
		}
	}

	/**
	 * Muestra en pantalla los errores de las clases de DecPHP Framework.
	 * @param  string $textError texto de error a mostrar.
	 */
	public static function error($textError = NULL)
	{
		if ($textError)
		{
			try
			{
				throw new Exception($textError);
			}
			catch (Exception $e)
			{
				if (self::getConfig('log_mode'))
				{
					$logText = date("d-M-Y g:i a")."\n";
					$logText .= 'ERROR: '.$e->getMessage()."\n";
					$logText .= 'SCRIPT: '.$e->getTrace()[1]['file']."\n";
					$logText .= 'LÍNEA: '.$e->getTrace()[1]['line']."\n";
					$logText .= 'CLASE: '.$e->getTrace()[1]['class']."\n";
					$logText .= 'MÉTODO: '.$e->getTrace()[1]['class'].'::'.$e->getTrace()[1]['function'].'()'."\n\n";

					file_put_contents(self::dir(self::getConfig('log_folder')).'Log_DecPHP.txt', $logText, FILE_APPEND);
				}

				if (self::getConfig('debug_mode'))
				{
					require_once __DIR__.self::DS.'..'.self::DS.'html'.self::DS.'error.phtml';
				}
				else
				{
					echo utf8_decode('Ha ocurrido un error en la aplicación.');
				}
			}
		}
		else
		{
			echo utf8_decode('Debe proveer un parámetro $textError a App::error().');
		}

		exit;
	}

	/**
	 * Despliega la ayuda de DecPHP Framework.
	 */
	public static function help()
	{
		require_once __DIR__.self::DS.'..'.self::DS.'html'.self::DS.'help.phtml';

		exit;
	}

}