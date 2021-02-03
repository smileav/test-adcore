<?php


		require_once('config.php');
		require_once(DIR_SYSTEM . 'db.php');
		require_once(DIR_SYSTEM . 'mysqli.php');
		require_once(DIR_SYSTEM . 'log.php');
		require_once(DIR_SYSTEM . 'response.php');

		$db = new DB(DB_DRIVER, DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);
		$response = new Response();

		$postData = file_get_contents('php://input');
		$data = json_decode($postData, true);


	if ($_SERVER['REQUEST_METHOD'] == 'POST' && validate($data)) {
		try {
			setData($db, $data);

			if (isset($_GET['all_data'])) {//дополнительно вывод всех данных, чтоб не лазить в БД при отладке
				$res = getAllData($db);
			} else {
				$res = getData($db);
			}
			$success = array(
				"status" => "success",
				"message" => $res
			);
			$response->addHeader('HTTP/1.1  200');
			$response->addHeader('Content-Type: application/json');
			$response->setOutput(json_encode($success));
			$response->output();
		} catch (Exception $ex) {
			$error = array(
				"status" => "error",
				"message" => $ex->getMessage() . ' on line ' . $ex->getLine()
			);
			sendError($response, $error);
		}
	} else {
		$error = array(
			"status" => "error",
			"message" => "Не верный запрос"
		);

		sendError($response, $error);
	}

	/**
	 * отправка json ответа с ошибкой и нужными заголовками
	 * @param Response $response объект
	 * @param array $error массив с кодом ошибки и сообщением
	 */
	function sendError($response, $error)
	{
		$response->addHeader('HTTP/1.1  400');
		$response->addHeader('Content-Type: application/json');
		$response->setOutput(json_encode($error));
		$response->output();
	}

	/**
	 * Минимальная валидация и запись в лог входящих данных
	 * @param array $data пришедшие данные
	 * @return bool
	 */
	function validate($data)
	{
		$log = new Log('log.txt');
		$log->write($data);

		if ($data === null || !isset($data['email'])) {

			return false;
		}
		return true;
	}

	/**
	 * Запись в БД
	 * @param DB $db объект БД
	 * @param array $data данные
	 */
	function setData($db, $data)
	{
		$db->query("INSERT INTO `user` (`email`,`name`) VALUES ('" . $db->escape($data['email']) . "','" . $db->escape($data['name']) . "') ON DUPLICATE KEY UPDATE `name`='" . $db->escape($data['name']) . "'");
		foreach ($data['colors'] as $color) {
			$db->query("INSERT INTO `colors` (`name`,`count`,`user_email`) VALUES ('" . $db->escape($color['name']) . "',
			'" . (int)$color['count'] . "','" . $db->escape($data['email']) . "') 
			ON DUPLICATE KEY UPDATE `name`='" . $db->escape($color['name']) . "',`count`='" . (int)$color['count'] . "'");
		}

	}

	/**
	 * выборка всех юзеров
	 * @param DB $db
	 * @return array результат выборки
	 */
	function getData($db)
	{
		return $db->query("SELECT * FROM `user` order by `name` ASC")->rows;
	}

	/**
	 * выборка всех данных, сделано дополнительно, работает при гет ?all_data
	 * @param $db
	 * @return array
	 */
	function getAllData($db)
	{
		$result = array();
		$user_data = $db->query("SELECT * FROM `user` order by `name` ASC")->rows;
		foreach ($user_data as $key => $ud) {
			$color_data = $db->query("SELECT * FROM `colors` WHERE `user_email`='" . $db->escape($ud['email']) . "'  order by `name` ASC")->rows;
			$result[$key] = $ud;
			$result[$key]['colors'] = $color_data;
		}
		return $result;
	}

