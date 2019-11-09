<?php

namespace AegisFang\Database;

use PDO;
use PDOException;

/**
 * Class Connection
 * @package AegisFang\Database
 */
class Connection {
	public static function make($config)
	{
		try {
			$options = getenv('DB_OPTIONS') ?: [PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING];
			return new PDO(
				getenv('DB_CONNECTION').':host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME'),
				getenv('DB_USERNAME'),
				getenv('DB_PASSWORD'),
				$options
			);
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}
