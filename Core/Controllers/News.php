<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 16:49
 */

namespace Brevis\Controllers;

use Brevis\Controller as Controller;


class News extends Controller {

	public $name = 'news';

	public $item = null;
	public $limit = 2;
	public $page = 1;
	private $_offset = 0;
	private $_total = 0;

	/**
	 * @param array $params
	 *
	 * @return bool
	 */
	public function initialize(array $params = array()) {
		if (empty($params)) {
			$this->redirect("/{$this->name}/");
		}
		elseif ($params[0] === '0') {
			$this->redirect("/{$this->name}/");
		}
		// После адреса страницы указан параметр
		elseif (!empty($params[0])) {
			// Указано число, значит это номер страницы
			// Реагируем только на вторую страницу и дальше
			if (is_numeric($params[0]) && $params[0] > 1) {
				// После номера нет косой, или наоборот, указано что-то еще
				if (!isset($params[1]) || !empty($params[1])) {
					// Делаем редирект на канонический адрес
					$this->redirect("/{$this->name}/$params[0]/");
				}
				// В противном случае, сохраняем номер страницы и считаем,
				// сколько строк нужно пропустить от начала в выборке
				$this->page = (int)$params[0];
				$this->_offset = ($this->page - 1) * $this->limit;
			}
			// Указано не число - это alias новости
			else {
				// Здесь всё осталось как раньше, только кода поменьше
				$c = $this->core->xpdo->newQuery('Brevis\Model\News', array('alias' => $params[0]));
				if ($news = @$this->core->xpdo->getObject('Brevis\Model\News', $c)) {
					$this->item = $news;
				}
			}
			// Если не выбрана заметка и offset пустой, то делаем редирект в корень раздела
			// Это будет в случае, если в параметрах указана какая-то ерунда
			if (!$this->_offset && !$this->item && !$this->isAjax) {
				$this->redirect("/{$this->name}/");
			}
		}

		return true;
	}

	/**
	 * Основной рабочий метод
	 *
	 * @return string
	 */
	public function run() {

		if ($this->isAjax) {
			if ($this->item) {
				$this->core->ajaxResponse(false, 'Контроллер News не принимает ajax в режиме показа отдельной новости');
			}

 			$items = $this->getItems();
 			$pagination = $this->getPagination($this->_total, $this->page, $this->limit);
			$this->core->ajaxResponse(true, '', array(
	 				'items' => $this->template('_news', array('items' => $items), $this),
	 				'pagination' => $this->template('_pagination', array('pagination' => $pagination), $this),
	 				'total' => $this->_total,
	 				'page' => $this->page,
	 				'limit' => $this->limit,
	 			));
 		}

		if ($this->item) {
			$data = array(
				'title' => $this->item->get('pagetitle'),
				'pagetitle' => $this->item->get('pagetitle'),
				'longtitle' => $this->item->get('longtitle'),
				'content' => $this->core->getParser()->text($this->item->get('text')),
			);
		}
		else {
			$data = array(
				'title' => 'Новости',
				'pagetitle' => 'Новости',
				'items' => @$this->getItems(),
				// Пагинация с нашими свойствами: total, page и limit
				'pagination' => $this->getPagination($this->_total, $this->page, $this->limit),
				'content' => '',
			);
		}

		return $this->template('news', $data, $this);
	}

	/**
	 * Выбор последних новостей с обрезкой текста
	 *
	 * @return array
	 */
	public function getItems() {
		$rows = array();
		$c = $this->core->xpdo->newQuery('Brevis\Model\News');
		// Считаем общее количество новостей
		$this->_total = $this->core->xpdo->getCount('Brevis\Model\News');
		// Если пропуск от начала больше, чем общее количество - указана несуществующая страница
		if ($this->_offset >= $this->_total) {
			// Редиректим в корень раздела
			$this->redirect("/{$this->name}/");
		}
		$c->select($this->core->xpdo->getSelectColumns('Brevis\Model\News', 'News'));
		$c->sortby('id', 'DESC');
		$c->limit($this->limit, $this->_offset);
		if ($c->prepare() && $c->stmt->execute()) {
			while ($row = $c->stmt->fetch(\PDO::FETCH_ASSOC)) {
				$cut = strpos($row['text'], "\n");
				if ($cut !== false) {
					$row['text'] = substr($row['text'], 0, $cut);
					$row['cut'] = true;
				}
				else {
					$row['cut'] = false;
				}
				$row['text'] = $this->core->getParser()->text($row['text']);
				$rows[] = $row;
			}
		}
		else {
			$this->core->log('Не могу выбрать новости:' . print_r($c->stmt->errorInfo(), true));
		}

		return $rows;
	}

}