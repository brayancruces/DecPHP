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

/** AUTO CARGA DE CLASES DE DecPHP FRAMEWORK. **/

spl_autoload_register(function($clase)
{
	$archivoClase = __DIR__.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.$clase.'.php';

	if (is_readable($archivoClase))
	{
		require_once $archivoClase;
	}
});