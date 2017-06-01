<?php
/**
 * Created by PhpStorm.
 * User: Misha
 * Date: 31.05.2017
 * Time: 15:57
 */

ini_set('display_errors', 1);
ini_set('error_reporting', -1);

require_once dirname(__FILE__) . '/vendor/autoload.php';

$Core = new \Brevis\Core();

$req = !empty($_REQUEST['q']) ? trim($_REQUEST['q']) : '';

$Core->handleRequest($req);