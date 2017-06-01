<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 16:49
 */

namespace Brevis\Controllers;

use Brevis\Controller as Controller;


class Test extends Controller {

	public $name = 'test';

	/**
	 * @param array $params
	 *
	 * @return bool
	 */
	public function initialize(array $params = array()) {
		if (empty($params)) {
			$this->redirect('/test/');
		}
		return true;
	}

	/**
	 * Основной рабочий метод
	 *
	 * @return string
	 */
	public function run() {
		return $this->template('test', array(
			'title' => 'Тестовая страница',
			'pagetitle' => 'Тестовая страница',
			'content' => 'Текст тестовой страницы',
		), $this);
	}

}