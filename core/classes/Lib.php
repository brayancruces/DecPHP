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

abstract class Lib extends DecPHP
{

	/**
	 * Carga una librería.
	 * @param  string $libs nombre del archivo del librería a cargar sin la extensión .php.
	 */
	public static function load($lib = NULL)
	{
		if ($lib)
		{
			$ruta = App::getConfig('libs_folder');

			if (stripos($lib, '.') !== FALSE)
			{
				$ruta = explode('.', $lib);
				
				$lib  = array_pop($ruta);
				
				$ruta = App::getConfig('libs_folder') ? App::getConfig('libs_folder').'.'.implode('.', $ruta) : implode('.', $ruta);
			}

			$ruta = self::dir($ruta).$lib.'.php';

			if (is_readable($ruta))
			{
				require_once $ruta;
			}
			else
			{
				App::error('No existe o no es legible la librería '.$ruta);
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $lib a App::loadLib().');
		}	
	}

}