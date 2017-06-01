<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 16:29
 */

namespace Brevis;

use \Fenom as Fenom;
use \Exception as Exception;

class Core {
	public $config = array();
	/** @var Fenom $fenom */
	public $fenom;
	/**
	 * Конструктор класса
	 *
	 * @param array $config
	 */

	function __construct(array $config = array())
	{
		$this->config = array_merge(
			array(
				'templatesPath' => dirname(__FILE__) . '/Templates/',
				'cachePath' => dirname(__FILE__) . '/Cache/',
				'fenomOptions' => array(
					'auto_reload' => true,
					'force_verify' => true,
				),
			),
			$config
		);

	}

	/**
	 * Обработка входящего запроса
	 *
	 * @param $uri
	 */

	public function handleRequest($uri) {
		// Определяем страницу для вывода
		$request = explode('/', $uri);

		$className = '\Brevis\Controllers\\' . ucfirst(array_shift($request));

		if (!class_exists($className)) {

			$controller = new Controllers\Home($this);
		}
		else {
			$controller = new $className($this); // Передавая экземпляр текущего класс в него - $this
		}

		$initialize = $controller->initialize($request);
		if ($initialize === true) {
			$response = $controller->run();
		}
		elseif (is_string($initialize)) {
			$response = $initialize;
		}
		else {
			$response = 'Возникла неведомая ошибка при загрузке страницы';
		}

		echo $response;
	}

	/**
	 * Получение экземпляра класса Fenom
	 *
	 * @return bool|Fenom
	 */
	public function getFenom() {
		// Работаем только, если переменная класса пуста
		if (!$this->fenom) {
			// Пробуем загрузить шаблонизатор
			// Все выброшенные исключения внутри этого блока будут пойманы в следующем
			try {

				// Проверяем и создаём директорию для кэширования скомпилированных шаблонов
				if (!file_exists($this->config['cachePath'])) {
					mkdir($this->config['cachePath']);
				}
				// Запускаем Fenom
				$this->fenom = Fenom::factory($this->config['templatesPath'], $this->config['cachePath'], $this->config['fenomOptions']);
			}
				// Ловим исключения, если есть, и отправляем их в лог
			catch (Exception $e) {
				$this->log($e->getMessage());
				// Возвращаем false
				return false;
			}
		}
		// Возвращаем объект Fenom
		return $this->fenom;
	}
	/**
	 * Метод удаления директории с кэшем
	 *
	 */
	public function clearCache() {
		$this->rmDir($this->config['cachePath']);
		mkdir($this->config['cachePath']);
	}
	/**
	 * Рекурсивное удаление директорий
	 *
	 * @param $dir
	 */
	public function rmDir($dir) {
		$dir = rtrim($dir, '/');
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (is_dir($dir . '/' . $object)) {
						$this->rmDir($dir . '/' . $object);
					}
					else {
						unlink($dir . '/' . $object);
					}
				}
			}
			rmdir($dir);
		}
	}
	/**
	 * Логирование. Пока просто выводит ошибку на экран.
	 *
	 * @param $message
	 * @param $level
	 */
	public function log($message, $level = E_USER_ERROR) {

		if (!is_scalar($message)) {

			$message = print_r($message, true);
		}

		trigger_error($message, $level);
	}
}