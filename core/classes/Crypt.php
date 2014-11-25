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

abstract class Crypt extends DecPHP
{

	/**
	 * Retorna $data serializada y encriptada. Así puede serializarse y encriptarse cualquier cosa.
	 * @param  mixed $data Data a ser serializada y encriptada.
	 * @return string      La $data serializada y encriptada.
	 */
	public static function encrypt($data = NULL)
	{
		if ($data)
		{
			if (App::getConfig('hash_key'))
			{
				$iv_size    = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
				
				$iv         = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				
				$encriptado = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, App::getConfig('hash_key'), serialize($data), MCRYPT_MODE_ECB, $iv);

				$encriptado = base64_encode($encriptado);

		        $encriptado = str_replace(array('+', '/' ,'='), array('-', '_', ''), $encriptado);

		        return $encriptado;
			}
			else
			{
				App::error('Debe proveer una clave hash_key con App::setConfig(\'hash_key\', \'\').');
			}
		}
		else
		{
			App::error('Debe proveer el parámetro $data a Crypt::encrypt($data).');
		}
	}

	/**
	 * Retorna $data deserializada y desencriptada.
	 * @param  mixed $data Data a ser deserializada y desencriptada.
	 * @return string      La $data deserializada y desencriptada.
	 */
	public static function decrypt($data = NULL)
	{
		if ($data)
		{
			if (App::getConfig('hash_key'))
			{
				$data = str_replace(array('-', '_'), array('+', '/'), $data);

		        $mod4 = strlen($data) % 4;
		        
		        if ($mod4) $data .= substr('====', $mod4);

				$encriptado    = base64_decode($data);
				
				$iv_size       = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
				
				$iv            = mcrypt_create_iv($iv_size, MCRYPT_RAND);
				
				$desencriptado = unserialize(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, App::getConfig('hash_key'), $encriptado, MCRYPT_MODE_ECB, $iv));

				return $desencriptado;
			}
			else
			{
				App::error('Debe proveer una clave hash_key con App::setConfig(\'hash_key\', \'\').');
			}
		}
		else
		{
			App::error('Debe proveer el parámetro $data a Crypt::decrypt($data).');
		}
	}

}