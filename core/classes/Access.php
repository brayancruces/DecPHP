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

abstract class Access extends DecPHP
{

	/**
	 * Devuelve el número correspondiente al $level de usuario.
	 * @param  string $level level (rol) de usuario.
	 * @return integer       Número entero correspondiente al level de usuario.
	 */
	private static function _level($level = NULL)
	{
		$access_levels = App::getConfig('access_levels');

		if (isset($access_levels) and is_array($access_levels))
		{
			if ($level)
			{
				if (array_key_exists($level, App::getConfig('access_levels')))
				{
					return App::getConfig('access_levels')[$level]; 
				}
				else
				{
					App::error('El nivel de acceso '.$level.' no existe entre los niveles de acceso permitidos.');
				}
			}
			else
			{
				App::error('Debe proveer un parámetro $level a Access::_level().');
			}
		}
		else
		{
			App::error('Debe establecer un arreglo con los niveles de acceso con App::setConfig(\'access_levels\',			array())');
		}
	}

	/**
	 * Permite el acceso para el level de usuario menor o igual al level establecido por $levelAccess.
	 * 'admin' => 1, 'especial' => 2, 'invitado' => 3
	 * @param  string $levelAccess Nivel de acceso establecido.
	 * @param  string $guestAccess Nivel de acceso del usuario.
	 * @return boolean             TRUE en caso de permitir el acceso, FALSE en caso contrario.
	 */
	public static function allow($levelAccess = NULL, $guestAccess = NULL)
	{
		if ($levelAccess)
		{
			if ($guestAccess)
			{
				if (self::_level($levelAccess) < self::_level($guestAccess))
				{
					return FALSE;
				}
				else
				{
					return TRUE;
				}
			}
			else
			{
				App::error('Debe proveer un parámetro $guestAccess a Access::allow($levelAccess, $guestAccess).');
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $levelAccess a Access::allow($levelAccess, $guestAccess).');
		}
	}

	/**
	 * Permite el acceso estricto para el level de usuario menor o igual al level establecido por $levelAccess.
	 * 'admin' => 1, 'especial' => 2, 'invitado' => 3
	 * @param  string $levelAccess Nivel de acceso establecido.
	 * @param  string $guestAccess Nivel de acceso del usuario.
	 * @return boolean             TRUE en caso de permitir el acceso, FALSE en caso contrario.
	 */
	public static function allowStrict($levelAccess = NULL, $guestAccess = NULL)
	{
		if ($levelAccess)
		{
			if ($guestAccess)
			{
				if (self::_level($levelAccess) == self::_level($guestAccess))
				{
					return TRUE;
				}
				else
				{
					return FALSE;
				}
			}
			else
			{
				App::error('Debe proveer un parámetro $guestAccess a Access::allowStrict($levelAccess, $guestAccess).');
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $levelAccess a Access::allowStrict($levelAccess, $guestAccess).');
		}
	}

}