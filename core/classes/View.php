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

abstract class View extends DecPHP
{
	/**
	 * html de la vista a ser mostrada.
	 * @var string
	 */
	private static $_vista;

	/**
	 * Arreglo que guarda los archivos javascript de la vista.
	 * @var array
	 */
	private static $_jsFiles = array();

	/**
	 * Arreglo que guarda los archivos css de la vista.
	 * @var array
	 */
	private static $_cssFiles = array();

	/**
	 * Renderiza una vista.
	 * @param  string  $vista        archivo de vista sin la extensión .phtml
	 * @param  array   $data         datos a ser pasados a la vista.
	 * @param  boolean $sinTemplate  determina si se muestra la vista sin template.
	 * @param  string  $fileTemplate archivo de template.
	 */
	public static function render($vista = NULL, $data = array(), $sinTemplate = FALSE, $fileTemplate = NULL)
	{
		if ($vista)
		{
			$ruta = App::getConfig('templates_folder');

			if (isset($fileTemplate))
			{
				if (stripos($fileTemplate, '.') !== FALSE)
				{
					$ruta         = explode('.', $fileTemplate);
					
					$fileTemplate = array_pop($ruta);
					
					$ruta         = App::getConfig('templates_folder') ? App::getConfig('templates_folder').'.'.implode('.', $ruta) : implode('.', $ruta);
				}
				
				$template = self::dir($ruta).$fileTemplate.'Template.phtml';
			}
			else
			{
				if (!App::getConfig('template')) $sinTemplate = TRUE;

				$template = self::dir($ruta).App::getConfig('template').'Template.phtml';
			}


			$ruta = App::getConfig('views_folder');

			if (stripos($vista, '.') !== FALSE)
			{
				$ruta  = explode('.', $vista);
				
				$vista = array_pop($ruta);
				
				$ruta  = App::getConfig('views_folder') ? App::getConfig('views_folder').'.'.implode('.', $ruta) : implode('.', $ruta);
			}
			
			$vista = self::dir($ruta).$vista.'View.phtml';


			if (!is_readable($vista))
			{
				App::error('No existe la vista '.$vista);
			}


			if (!$sinTemplate)	// Con template.
			{
				if (is_readable($template))
				{
					ob_start();

					require_once $vista;

					self::$_vista = ob_get_clean();

					require_once $template;
				}
				else
				{
					App::error('No existe el template '.$template);
				}
			}
			else // Sin template.
			{
				require_once $vista;
			}
		}
		else
		{
			App::error('Debe proveer una $vista a View::render().');
		}
	}

	/**
	 * Retorna la vista en html.
	 * @return string
	 */
	public static function html()
	{
		return self::$_vista;
	}

	/**
	 * Setea los archivos javascript de la vista.
	 * @return null.
	 */
	public static function setJs()
	{
		foreach (func_get_args() as $archivo)
		{
			$ruta = self::dir(App::getConfig('js_folder')).$archivo.'.js';

			if (is_readable($ruta))
			{
				self::$_jsFiles[] = $archivo;
			}
			else
			{
				App::error('No existe o no es legible el archivo '.$ruta);
			}
		}
	}

	/**
	 * Retorna los archivos js de la vista en formato html.
	 * @return string
	 */
	public static function js()
	{
		$jsFiles = '';

		$ruta = App::getConfig('js_folder') ? str_replace('.', '/', App::getConfig('js_folder')).'/' : '';
		
		foreach (self::$_jsFiles as $archivo)
		{
			$jsFiles .= '<script src="'.self::url().'/'.$ruta.$archivo.'.js" type="text/javascript"></script>'."\n";
		}

		return $jsFiles;
	}

	/**
	 * Setea los archivos css de la vista.
	 * @return null.
	 */
	public static function setCss()
	{
		foreach (func_get_args() as $archivo)
		{
			$ruta = self::dir(App::getConfig('css_folder')).$archivo.'.css';

			if (is_readable($ruta))
			{
				self::$_cssFiles[] = $archivo;
			}
			else
			{
				App::error('No existe o no es legible el archivo '.$ruta);
			}
		}
	}

	/**
	 * Retorna los archivos css de la vista en formato html.
	 * @return string
	 */
	public static function css()
	{
		$cssFiles = '';

		$ruta = App::getConfig('css_folder') ? str_replace('.', '/', App::getConfig('css_folder')).'/' : '';
		
		foreach (self::$_cssFiles as $archivo)
		{
			$cssFiles .= '<link rel="stylesheet" href="'.self::url().'/'.$ruta.$archivo.'.css">'."\n";
		}

		return $cssFiles;
	}

}