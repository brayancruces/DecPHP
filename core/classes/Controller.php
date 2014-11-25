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

abstract class Controller extends DecPHP
{

	public function __construct()
	{

	}

	/**
	 * Todos los controladores de la aplicación deben implementar un método index.
	 */
	abstract function index();

}