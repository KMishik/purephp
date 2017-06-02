<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 16:29
 */

namespace Brevis;

use \Fenom as Fenom;
use \xPDO\xPDO as xPDO;
use \Exception as Exception;

class Core {

	public $config = array();

	/** @var Fenom $fenom */
	public $fenom;

	/** @var xPDO $xpdo  */
	public $xpdo;

	/**
	 * Конструктор класса
	 *
	 * @param string $config Имя файла с конфигом
	 */
	function __construct($config = 'config') {
		if (is_string($config)) {
			$config = dirname(__FILE__) . "/Config/{$config}.inc.php";
			if (file_exists($config)) {
				require $config;
				/** @var string $database_dsn */
				/** @var string $database_user */
				/** @var string $database_password */
				/** @var array $database_options */
				try {
					$this->xpdo = new xPDO($database_dsn, $database_user, $database_password, $database_options);
					$this->xpdo->setPackage('Model', PROJECT_CORE_PATH);
					$this->xpdo->startTime = microtime(true);
				}
				catch (Exception $e) {
					exit($e->getMessage());
				}
			}
			else {
				exit('Не могу загрузить файл конфигурации');
			}
		}
		else {
			exit('Неправильное имя файла конфигурации');
		}

		$this->xpdo->setLogLevel(defined('PROJECT_LOG_LEVEL') ? PROJECT_LOG_LEVEL : xPDO::LOG_LEVEL_ERROR);
		$this->xpdo->setLogTarget(defined('PROJECT_LOG_TARGET') ? PROJECT_LOG_TARGET : 'FILE');
	}

	/**
	 * Удаление ненужных файлов в пакетах, установленных через Composer
	 *
	 * @param mixed $base
	 */
	public static function cleanPackages($base = '') {
		// Composer при вызове метода передаёт внутрь свой объект, но нам это не нужно
		// Значит, если передана не строка, то это первый запуск и мы стартуем от директории вендоров
		if (!is_string($base)) {
			$base = dirname(dirname(__FILE__)) . '/vendor/';
		}
		// Получаем все директории и
		if ($dirs = @scandir($base)) {
			// Проходим по ним в цикле
			foreach ($dirs as $dir) {
				// Символы выхода из директории нас не интересуют
				if (in_array($dir, array('.', '..'))) {
					continue;
				}
				$path = $base . $dir;
				// Если это директория, а не файл
				if (is_dir($path)) {
					// И она в следующем списке
					if (in_array($dir, array('tests', 'test', 'docs', 'gui', 'sandbox', 'examples', '.git'))) {
						// Удаляем её, вместе с поддиректориями
						Core::rmDir($path);
					}
					// А если не в списке - рекурсивно проверяем её дальше, этим же методом
					else {
						// Просто передавая в него нужный путь
						Core::cleanPackages($path . '/');
					}
				}
				// А если это файл, то удаляем все, кроме php
				elseif (pathinfo($path, PATHINFO_EXTENSION) != 'php') {
					unlink($path);
				}
			}
		}
	}

	/**
	 * Рекурсивное удаление директорий
	 *
	 * @param $dir
	 */
	public static function rmDir($dir) {
		$dir = rtrim($dir, '/');
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != '.' && $object != '..') {
					if (is_dir($dir . '/' . $object)) {
						Core::rmDir($dir . '/' . $object);
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
				if (!file_exists(PROJECT_CACHE_PATH)) {
					mkdir(PROJECT_CACHE_PATH);
				}
				// Запускаем Fenom
				$this->fenom = Fenom::factory(PROJECT_TEMPLATES_PATH, PROJECT_CACHE_PATH, PROJECT_FENOM_OPTIONS);
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
		Core::rmDir(PROJECT_CACHE_PATH);
		mkdir(PROJECT_CACHE_PATH);
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