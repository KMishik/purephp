<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 16:49
 */

namespace Brevis\Controllers;

use Brevis\Controller as Controller;

if (!class_exists('Controller')) {
	require_once dirname(dirname(__FILE__)) . '/Controller.php';
}


class Home extends Controller {

	/**
	 * @param array $params
	 *
	 * @return bool
	 */
	public function initialize(array $params = array()) {
		if (!empty($_REQUEST['q'])) {
			$this->redirect('/');
		}
		return true;
	}

	/**
	 * Основной рабочий метод
	 *
	 * @return string
	 */
	public function run()
	{
		return $this->template('home', array(
			'title' => 'Главная страница',
			'pagetitle' => 'Третий курс обучения',
			'content' => 'Текст главной страницы',
		), $this);
	}
}
