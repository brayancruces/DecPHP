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

abstract class Model extends DecPHP
{
	/**
	 * Bandera que notifica de un error en la base de datos / modelo.
	 * @var boolean
	 */
	private static $_modelError;
	
	/**
	 * Constructor de la clase, inicializa la bandera de error a false.
	 */
	public function __construct()
	{
		self::$_modelError = FALSE;
	}

	/**
	 * Ejecuta una sentencia SQL en la base de datos establecida.
	 * @param  string $sql    sentencia SQL.
	 * @param  array  $params parámetros de la sentencia SQL.
	 * @return array/NULL     conjunto de datos para una sentencia SELECT o NULL en cualquier otro caso.
	 */
	protected static function sql($sql = NULL, $params = array())
	{
		if ($sql)
		{	
			$driver = App::getConfig('db_driver');
			$host   = App::getConfig('db_host');
			$port   = App::getConfig('db_port');
			$dbname = App::getConfig('db_dbname');
			$user   = App::getConfig('db_user');
			$pass   = App::getConfig('db_pass');
			
			$dsn = $driver.':host='.$host.';port='.$port.';dbname='.$dbname;

			try
			{
				$db = new PDO($dsn, $user, $pass);

				$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
				$data = $db->prepare($sql);

				$data->execute($params);

				if (strtoupper(substr($sql, 0, 6)) === strtoupper('SELECT'))
				{

					return $data->fetchAll(PDO::FETCH_ASSOC);
				}
				else
				{
					return $data->rowCount();
				}		
			}
			catch(PDOException $e)
			{
				self::$_modelError = TRUE;

				if (App::getConfig('debug_mode'))
				{
					App::error('Base de Datos: '.$e->getMessage());
				}	
			}		
		}
		else
		{
			App::error('Debe proveer una sentencia SQL a Model::sql($sql).');
		}
	}

	/**
	 * Retorna el estado de error de la última transacción en la base de datos.
	 * @return boolean TRUE si hubo un error, FALSE en caso contrario.
	 */
	public static function modelError()
	{
		return self::$_modelError;
	}

	/**
	 * Carga un modelo.
	 * @param  string $model nombre del archivo del modelo a cargar sin la extensión .php.
	 * @return Object        Devuelve una instancia del modelo solicitado.
	 */
	public static function load($model = NULL)
	{
		if ($model)
		{
			$ruta = App::getConfig('models_folder');

			if (stripos($model, '.') !== FALSE)
			{
				$ruta  = explode('.', $model);
				
				$model = array_pop($ruta);
				
				$ruta  = App::getConfig('models_folder') ? App::getConfig('models_folder').'.'.implode('.', $ruta) : implode('.', $ruta);
			}

			$ruta = self::dir($ruta).$model.'Model.php';

			if (is_readable($ruta))
			{
				require_once $ruta;

				$model = $model.'Model';

				return new $model;
			}
			else
			{
				App::error('No existe o no es legible el modelo '.$ruta);
			}
		}
		else
		{
			App::error('Debe proveer un parámetro $model a Model::load().');
		}
	}

}