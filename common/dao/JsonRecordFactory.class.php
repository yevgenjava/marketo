<?php
require_once(APP_ROOT . "info/InfoFactory.interface.php");
require_once(APP_ROOT . "info/JsonRecord.class.php");

/**
 * Info Factory implementation for the JsonRecord object, which maps
 * columns from the database to fields in the object.
 * 
 * @author yevgen.java@gmail.com
 */
class JsonRecordFactory implements InfoFactory
{
	/**
	 * Creates JsonRecordFactory object from given database row (as array).
	 *
	 * @param array $record database record row as array.
	 * @return JsonRecord object with all properties set from db.
	 */
	public function &create(&$record)
	{
		$info = new JsonRecord();
		
		$info->setId($record["id"]);
		$info->setName($record["name"]);
		$info->setValue($record["value"]);
		$info->setTimestamp($record["timestamp"]);
		
		return $info;
	}
}
?>