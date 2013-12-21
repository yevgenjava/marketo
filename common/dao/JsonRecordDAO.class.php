<?php
require_once(APP_ROOT . "dao/DAO.class.php");
require_once(APP_ROOT . "dao/JsonRecordFactory.class.php");

/**
 * DAO for JsonRecord object.
 *
 * @author yevgen.java@gmail.com
 */
class JsonRecordDAO extends DAO
{
	private static $TABLE_NAME = "json_records";

	/**
	 * Finds JsonRecord by id.
	 *
	 * @param long $id record id.
	 * @return JsonRecord object that was found, or null if nothing found in database.
	 */
	public function getJsonRecordById($id)
	{
		$sql = "SELECT * FROM {$this->getTable()} WHERE id = '" . addslashes($user_id) . "' LIMIT 1";

		return parent::querySingle($sql, new JsonRecordFactory());
	}

	/**
	 * Creates or updates json info record in database.
	 *
	 * @param JsonRecord $json_record object to create or update.
	 */
	public function createOrUpdate(JsonRecord &$json_record)
	{
		// We can also use INSERT DELAYED here, etc., but I'm not aware of the scope where this could be 
		// used or what you guys have in mind for this test. 
		$sql =  "INSERT IGNORE INTO {$this->getTable()} ("
					. " id, name, value, timestamp "
					. ") VALUES("
					. "'" . mysql_escape_string($json_record->getId())        . "', "
					. "'" . mysql_escape_string($json_record->getName())      . "', "
					. "'" . mysql_escape_string($json_record->getValue())     . "', "
					. "FROM_UNIXTIME('" . mysql_escape_string($json_record->getTimestamp()) . "') "
					. ") ON DUPLICATE KEY UPDATE "
					. " name      = '" . mysql_escape_string($json_record->getName())      . "',"
					. " value     = '" . mysql_escape_string($json_record->getValue())     . "',"
					. " timestamp = FROM_UNIXTIME('" . mysql_escape_string($json_record->getTimestamp()) . "')";
		
		if(defined(SQLDEBUG) && SQLDEBUG)
		{
			LogUtil::debug("JsonRecordDAO::createOrUpdate SQL: " + $sql);
		}
		
		return parent::executeUpdate($sql);
	}

	/**
	 * Returns table associated with this DAO.
	 *
	 * @return string table name for this DAO object.
	 */
	protected static function getTable()
	{
		return self::$TABLE_NAME;
	}
}
?>
