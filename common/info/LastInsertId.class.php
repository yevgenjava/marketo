<?php
/**
 * Holds last_insert_id value.
 * 
 * @author yevgen.java@gmail.com
 */
class LastInsertId
{
	public $last_insert_id;

	public function setLastInsertId($id)
	{
		$this->last_insert_id = $id;
	}

	/**
	 * Gets last inserted id for current mysql connection after insert
	 * statement.
	 */
	public function getLastInsertId()
	{
		return $this->last_insert_id;
	}
}
?>