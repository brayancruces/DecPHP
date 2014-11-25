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

abstract class Upload extends DecPHP
{

	/**
	 * Sube al servidor archivos enviados por el usuario.
	 * @param  string  $inputName   Variable FILE que viene por POST a ser usada.
	 * @param  array   $validTypes  Tipos de mimes válidos.
	 * @param  integer $maxSizeFile Tamaño máximo de los archivos en bytes.
	 * @param  string  $path        Ruta donde se guardarán los archivos en el servidor.
	 * @param  [type]  $prefix      Prefijo para los nombres de archivos.
	 * @return FALSE / Array        De no subirse ningún archivo retornará FALSE. Retornará las URL de los archivos subidos y los errores del upload.
	 */
	public static function run($inputName = NULL, $validTypes = array(), $maxSizeFile = 0, $path = NULL, $prefix = NULL)
	{
		if ($inputName)
		{
			if(empty($_FILES))
			{
				return FALSE;
			}

			$_error = array(
				1 => 'El tamaño del archivo ha excedido al máximo establecido en la directiva upload_max_filesize del archivo de configuración de Php.',
				2 => 'El tamaño del archivo ha excedido el valor MAX_FILE_SIZE especificado en el formulario de HTML.',
				3 => 'El archivo se subió incompleto o parcialmente al servidor.',
				4 => 'El archivo no fue subido al servidor.',
				6 => 'No se encuentra el directorio temporal en el servidor.',
				7 => 'Error de escritura del archivo en el servidor.',
				8 => 'PHP-stop downloading the file extension. PHP does not provide a way determine what extension stop file upload.'
			);
			
			$archivos = array();
			$errores  = array();
			$movidos  = array();

			foreach($_FILES[$inputName]['name'] as $i => $name)
			{
				if ($_FILES[$inputName]['error'][$i] == 0)
				{
					if (in_array($_FILES[$inputName]['type'][$i], $validTypes))
					{
						if ($_FILES[$inputName]['size'][$i] <= $maxSizeFile)
						{
							$archivo = array(	'name'     => $name,
												'tmp_name' => $_FILES[$inputName]['tmp_name'][$i]
											);

							if (!in_array($archivo, $archivos))
							{
								$archivos[] = array(	'name'     => $name,
														'tmp_name' => $_FILES[$inputName]['tmp_name'][$i]
													);
							}
						}
						else
						{
							// Error: el archivo supera el máximo del tamaño requerido.
							$errores[] = array('name' => $name, 'error' => $_error[2]);
						}
					}
					else
					{
						// Error: el archivo dado no es del tipo requerido.
						$errores[] = array('name' => $name, 'error' => 'Tipo de archivo incorrecto.');
					}
				}
				else
				{
					// Error al subir el archivo al servidor.
					$errores[] = array('name' => $name, 'error' => $_error[$_FILES[$inputName]['error'][$i]]);
				}
			}
			/**
			 * GUARDADO  DE  LOS  ARCHIVOS. 
			 */
			if ($archivos)
			{
				$pathUrl = $path ? trim(self::url('/'.str_replace('.', '/', $path)), '/') : trim(self::url('/'.str_replace('.', '/', App::getConfig('data_folder'))), '/');
				
				$path    = $path ? self::dir($path) : self::dir(App::getConfig('data_folder'));
				
				$prefix  = $prefix ? $prefix : '';

				foreach ($archivos as $i => $item)
				{
					if (!move_uploaded_file($item['tmp_name'], $path . $prefix . $item['name']))
					{
						$errores[] = array('name' => $item['name'], 'error' => 'Hubo un error al guardar el archivo en el servidor.');
					}
					else
					{
						$movidos[] = array('name' => $item['name'], 'url' => $pathUrl.'/'.$prefix.$item['name']);
					}
				}
			}
			else
			{
				return FALSE;
			}

			return array('archivos' => $movidos, 'errores' => $errores);
		}
		else
		{
			App::error('Debe proveer un parámetro $inputName a Upload::run().');
		}
	}

}