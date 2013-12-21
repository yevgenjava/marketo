<?php
/**
 * Represends expected JSON object from remote server endpoint.
 * <p>
 * From the requirements:<br/>
 * # Working assumptions :
 * - in production, the script will be run of crond on a minute frequency
 * - object JSON format: {"name":"","id":0,"value":"","timestamp":""}, where id and name are unique. 
 * </p>
 * @author yevgen.java@gmail.com
 */
class JsonRecord
{
	/**
	 * Unique ID.
	 * 
	 * @var $id unique identified (user_id, app_id, company_id, etc.)
	 */
	private $id;
	
	/**
	 * Unique name associated with $id.
	 * 
	 * @var string $name unique name.
	 */
	private $name;
	
	/**
	 * Some kind of value for this record.
	 * 
	 * @var string $value
	 */
	private $value;
	
	/**
	 * Unix timestamp of this record from remote server endpoint.
	 * 
	 * @var long $timestamp unix timestamp.
	 */
	private $timestamp;
	
	
	/* Getters/Setters */
	public function getId()
	{
		return $this->id;
	}
	
	public function setId($id)
	{
		$this->id = $id;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function getTimestamp()
	{
		return $this->timestamp;
	}
	
	public function setTimestamp($timestamp)
	{
		$this->timestamp = $timestamp;
	}
}
?>
