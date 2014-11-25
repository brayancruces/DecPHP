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

abstract class DecPHP
{
	/**
	 * Separador de directorio en el servidor.
	 */
	const DS = DIRECTORY_SEPARATOR;

	/**
	 * Retorna el directorio base del script que lo invoca.
	 * @return string retorna el directorio base del script que lo invoca.
	 */
	private static function base_dir()
	{
		return dirname($_SERVER['SCRIPT_FILENAME']).self::DS;
	}

	/**
	 * Retorna una ruta valida en el servidor, sino muestra un error. Admite la nomenclatura "directorio.subdirectorio.subdirectorio..."
	 * @param  string $path ruta en el directorio separado por punto.
	 * @return string ruta completa en el servidor validada.
	 */
	protected static function dir($path = NULL)
	{
		if ($path)
		{
			$path = self::base_dir().str_replace('.', self::DS, $path).self::DS;

			if (is_dir($path))
			{
				return $path;
			}
			else
			{
				App::error('No existe el directorio '.$path);
			}
		}
		else
		{
			return self::base_dir();
		}
	}

	/**
	 * Retorna la url base del script que lo invoca.
	 * @return string retorna la url base del script que lo invoca.
	 */
	private static function base_url()
	{
		return 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']);
	}

	/**
	 * Retorna una url completa valida del proyecto.
	 * @param  string $url url valida.
	 * @return string url completa y valida.
	 */
	protected static function url($url = NULL)
	{
		return $url ? self::base_url().$url : self::base_url();
	}

}