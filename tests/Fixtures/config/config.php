<?php

use AegisFang\Database\MySqlConnection;
use AegisFang\Log\LogToFile;

return [
    'logger' => LogToFile::class,
    'db_driver' => MySqlConnection::class,
];
