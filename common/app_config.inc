<?php
error_reporting(E_ALL & ~E_NOTICE);

define("APP_ROOT", realpath( dirname( __FILE__ ) ) . "/");
define("APP_NAME", "Marketo Interview Impl");
define("DATABASE_HOST", "localhost");
define("DATABASE_USER", "user");
define("DATABASE_PASS", "passwd");
define("DATABASE_NAME", "testdb");

define("DEBUG", false);
define("SQLDEBUG", false);
define("LOG_STDOUT", false);
require_once(APP_ROOT . "util/LogUtil.class.php");
require_once(APP_ROOT . "util/WebUtil.class.php");
require_once(APP_ROOT . "util/WebResponse.class.php");
require_once(APP_ROOT . "dao/JsonRecordDAO.class.php");
?>
