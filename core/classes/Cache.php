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

abstract class Cache extends DecPHP
{

	/**
	 * Coloca en la caché la data $data bajo una $clave.
	 * @param  string $clave una clave única mediante la cual se recuperará la $data en caché.
	 * @param  mixed $data  cualquier estructura de dato/archivo/objeto para guardar en caché.
	 */
	public static function put($clave = NULL, $data = NULL)
	{
		if ($clave)
		{
			if ($data)
			{
				$archivo = self::dir(App::getConfig('cache_folder')).Hash::get($clave);

                $data = Crypt::encrypt($data);

                file_put_contents($archivo, $data);
			}
			else
			{
				App::error('Debe proveer un parámetro $data a Cache::put($clave, $data).');
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $clave a Cache::put($clave, $data).');	
		}
	}

	/**
	 * Obtiene la $data en caché que ha sido guardada con una $clave y una antiguedad
	 * de tiempo máximo de $tiempoMaximo minutos.
	 * @param  string  $clave        clave de la cache.
	 * @param  integer $tiempoMaximo tiempo máximo de antiguedad de la caché en minutos. 
	 * @return mixed/false  mixed si encuentra la $data en cache, false si no la encuentra.
	 */
	public static function get($clave = NULL, $tiempoMaximo = 1)
	{
		if ($clave)
        {
            $archivo = self::dir(App::getConfig('cache_folder')).Hash::get($clave);

            if (is_file($archivo))
            {
            	if (filemtime($archivo) + $tiempoMaximo * 60 > time())
                {    
                	$data = file_get_contents($archivo);

                    return Crypt::decrypt($data);
                }
                else
                {
                    unlink($archivo);

                    return FALSE;
                }
            }
            else
            {
            	return FALSE;
            }
        }
        else
        {
        	App::error('Debe proveer un parámetro $clave al método Cache::get($clave).');
        }
	}

}