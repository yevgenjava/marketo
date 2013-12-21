<?php
require_once(APP_ROOT . "info/InfoFactory.interface.php");
require_once(APP_ROOT . "info/LastInsertId.class.php");

/**
 * @author yevgen.java@gmail.com
 */
class LastInsertIdFactory implements InfoFactory
{
	public function &create(&$record)
	{
		$last_insert_id = new LastInsertId();
		$last_insert_id->setLastInsertId($record["last_insert_id"]);

		return $last_insert_id;
	}
}
?>