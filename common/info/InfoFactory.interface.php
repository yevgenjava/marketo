<?php
/**
 * Info object factory interface.
 *
 * Interface to force some must-have methods on factory implementations.
 *
 * @author yevgen.java@gmail.com
 */
interface InfoFactory
{
	/**
	 * Creates record in database.
	 *
	 * @param object $record
	 */
	public function &create(&$record);
}
?>
