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

abstract class Widget extends DecPHP
{
	/**
	 * Almacena los widgets a ser mostrados en una vista.
	 * @var array
	 */
	private static $_widgets  = array();

	/**
	 * Setea una etiqueta en la vista/plantilla donde puede ir un widget.
	 * @param string $etiqueta Establece una etiqueta donde puede ir un widget.
	 */
	public static function set($etiqueta = NULL)
	{
		if ($etiqueta)
		{
			if (isset(self::$_widgets[$etiqueta]))
			{
				// El extract() deja disponible el $file y $data del widget .
				extract(array_values(self::$_widgets[$etiqueta])[0]);

				require $file;
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $etiqueta a Widget::set($etiqueta).');
		}
	}

	/**
	 * Habilita un widget en una etiqueta pre-establecida en una vista o plantilla.
	 * @param  string $etiqueta Etiqueta donde se mostrará el widget.
	 * @param  string $widget   Nombre del widget.
	 * @param  array  $data     Data a ser pasada al widget si fuera necesario.
	 */
	public static function render($etiqueta = NULL, $widget = NULL, $data = array())
	{
		if ($etiqueta)
		{
			if ($widget)
			{
				$ruta = App::getConfig('widgets_folder');

				if (stripos($widget, '.') !== FALSE)
				{
					$ruta   = explode('.', $widget);
					
					$widget = array_pop($ruta);
					
					$ruta   = App::getConfig('widgets_folder') ? App::getConfig('widgets_folder').'.'.implode('.', $ruta) : implode('.', $ruta);
				}
				
				$archivoWidget = self::dir($ruta).$widget.'Widget.phtml';

				if(is_readable($archivoWidget))
				{
					self::$_widgets[$etiqueta][$widget]['file'] = $archivoWidget;
					self::$_widgets[$etiqueta][$widget]['data'] = $data;
				}
				else
				{
					App::error('No existe o no es legible el widget '.$archivoWidget);
				}
			}
			else
			{
				App::error('Debe proveer un parámetro $widget a Widget::render($etiqueta, $widget).');
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $etiqueta a Widget::render($etiqueta, $widget).');
		}
	}

}