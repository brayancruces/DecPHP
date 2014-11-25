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

abstract class File extends DecPHP
{

	/**
	 * Mueve un archivo de una locación a otra.
	 * @param  string  $pathToFile Ruta o directorio que incluye el archivo a ser movido.
	 * @param  string  $dirToMove  Ruta o directorio donde se moverá el archivo.
	 * @param  boolean $del        Establece si el archivo movido se eliminará de su ubicación de orígen.
	 * @return boolean             Retorna TRUE si se movió el archivo, FALSE en caso contrario.
	 */
	public static function move($pathToFile = NULL, $dirToMove = NULL, $del = TRUE)
	{
		if ($pathToFile)
    	{
    		if ($dirToMove)
	    	{
				$pathinfo = pathinfo($pathToFile);

				$result = copy($pathToFile, $dirToMove.$pathinfo['basename']);

				if($del === TRUE) self::delete($pathToFile);

				return $result;
	    	}
	    	else
	    	{
	    		App::error('Debe proveer un parámetro $dirToMove a File::move().');
	    	}
    	}
    	else
    	{
    		App::error('Debe proveer un parámetro $pathToFile a File::move().');
    	}
	}

	/**
	 * Elimina un archivo del servidor.
	 * @param  string $pathToFile Ruta o directorio del archivo que incluye el nombre del archivo.
	 * @return boolean            Retorna TRUE si el archivo fue borrado, FALSE en caso contrario.
	 */
	public static function delete($pathToFile = NULL)
	{
		if ($pathToFile)
		{
			if (is_file($pathToFile))
			{
				return unlink($pathToFile) ? TRUE : FALSE;
			}
			else
			{
				App::error('No existe el archivo a ser borrado '.$pathToFile);		
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $pathToFile a File::delete().');
		}
	}

}