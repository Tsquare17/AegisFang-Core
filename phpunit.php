<?php

use AegisFang\Database\MySqlConnection;
use AegisFang\Log\LogToFile;

putenv('APP_CONFIG=' . __DIR__ . '/tests/Fixtures/config/config.php');

require_once __DIR__ . '/tests/Fixtures/Foo.php';
require_once __DIR__ . '/tests/Fixtures/Bar.php';
require_once __DIR__ . '/tests/Fixtures/env.php';
require_once __DIR__ . '/tests/Fixtures/Json.php';
require_once __DIR__ . '/tests/Fixtures/Rest.php';
require_once __DIR__ . '/tests/Fixtures/Middleware.php';
require_once __DIR__ . '/tests/Fixtures/SecondMiddleware.php';
