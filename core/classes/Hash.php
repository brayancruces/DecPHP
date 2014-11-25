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

abstract class Hash extends DecPHP
{

	/**
	 * Retorna el hash de $data.
	 * @param  string $data Data a transformarla en hash
	 * @return string       El hash de $data.
	 */
	public static function get($data = NULL)
	{
		if ($data)
		{
			if (in_array(App::getConfig('hash_algorithm'), hash_algos()))
			{
				if (App::getConfig('hash_key'))
				{
					$hash = hash_init(App::getConfig('hash_algorithm'), HASH_HMAC, App::getConfig('hash_key'));

					hash_update($hash, $data);

					return hash_final($hash);
				}
				else
				{
					App::error('Debe proveer una clave hash_key con App::setConfig(\'hash_key\', \'\').');
				}
			}
			else
			{
				App::error('El algoritmo dado '.App::getConfig('hash_algorithm').' no es válido. Debe establecerlo con App::setConfig(\'hash_algorithm\', \'\').');
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $data a Hash::get($data).');
		}
	}

}