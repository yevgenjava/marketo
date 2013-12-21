<?php
require_once(APP_ROOT . "util/LogUtil.class.php");
require_once(APP_ROOT . "info/LastInsertIdFactory.class.php");

/**
 * DAO class that provides connection and common methods for database access.
 *
 * @author yevgen.java@gmail.com
 */
class DAO
{
	protected $resource = null;
	// protected $dblink = null;
	private $start_time = 0;

	/**
	 * Default constructor.
	 */
	public function __construct()
	{
	/*
	 * // I've decided not to bother with mysqli, mysqlnd (native driver) connects, setups, etc.
	 * // since this is a simple app, but you got an idea what in real world it could be.
	 * 
		if(function_exists("mysqli_connect"))
		{
			$this->dblink = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS, DATABASE_NAME);
				
			if (mysqli_connect_error())
			{
				LogUtil::error("MySQLi connection error (" . mysqli_connect_errno() . ") " . mysqli_connect_error());
			}
		}
		else
		{*/
			$this->resource = mysql_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASS);
			$selected_db = mysql_select_db(DATABASE_NAME, $this->resource);
				
			if(!$this->resource)
			{
				LogUtil::error("Cannot connect to database: " . mysql_error());
			}
				
			if(!$selected_db)
			{
				LogUtil::error("Cannot select database: " . mysql_error());
			}
		//}

		if(SQLDEBUG)
		{
			LogUtil::debug("Database information: host=" . DATABASE_HOST . ", user=" . DATABASE_USER
			. ", pass=" . DATABASE_PASS . ", dbname=" . DATABASE_NAME);
		}
	}

	/**
	 * Default destructor.
	 */
	public function __destruct()
	{
		/*if(isset($this->dblink) && $this->dblink != null)
		{
			if(SQLDEBUG)
			{
				LogUtil::debug("DB::__destruct: mysqli dblink: " . var_export($this->dblink));
			}
			
			try 
			{
				mysqli_close($this->dblink);
			}
			catch(Exception $e)
			{
				LogUtil::warning("Failed to free/close mysqli dblink identifier: " 
					. $e->getMessage() . "; mysqli error: " . mysqli_error());
			}
		}
		else 
		*/
		if(isset($this->resource) && $this->resource != null)
		{
			if(SQLDEBUG)
			{
				LogUtil::debug("DB::__destruct: mysql resource: " . var_export($this->resource));
			}
			
			try
			{
				//non-persistent open links are automatically closed at the end of the script's execution
				//mysql_close($this->resource);
			}
			catch(Exception $e)
			{
				LogUtil::warning("Failed to free/close mysql link identifier: " . $e->getMessage() . "; mysql error: " . mysql_error());
			}
		}
	}

	/**
	 * Queries array of objects from DB.
	 *
	 * @param string $sql
	 * @param InfoFactoryInterface $factory implementation
	 */
	function &queryArray($sql, &$factory)
	{
		$queryId = mysql_query($sql, $this->resource);
		$ret = array();

		if(SQLDEBUG)
		{
			LogUtil::debug("DB::queryArray: SQL: " . $sql);
		}

		if($queryId)
		{
			while($record = mysql_fetch_array($queryId))
			{
				array_push($ret, $factory->create($record));
			}

			mysql_free_result($queryId);
		}
		else
		{
			LogUtil::warning("Cannot query array on database: " . mysql_error($this->resource) ."; SQL: " . $sql);
		}

		if(count($ret) == 0)
		{
			return null;
		}

		return $ret;
	}

	/**
	 * Queries single record from the database.
	 *
	 * @param string $sql
	 * @param InfoFactoryInterface $factory implementation
	 */
	function &querySingle($sql, &$factory)
	{
		$queryId = mysql_query($sql, $this->resource);

		if(SQLDEBUG)
		{
			LogUtil::debug("DB::querySingle: SQL: " . $sql);
		}

		if($queryId)
		{
			if($record = mysql_fetch_array($queryId))
			{
				mysql_free_result($queryId);
				return $factory->create($record);
			}
		}
		else
		{
			LogUtil::warning("Cannot query single on database: " . mysql_error($this->resource) . "; SQL: " . $sql);
		}

		return null;
	}

	/**
	 * Executes update on existing record in database.
	 *
	 * @param string $sql SQL to execute.
	 */
	function &executeUpdate($sql)
	{
		$result = mysql_query($sql, $this->resource);

		if(SQLDEBUG)
		{
			LogUtil::debug("DB::executeUpdate: SQL: " . $sql);
		}
		
		if($result && mysql_affected_rows($this->resource) > 0)
		{
			return true;
		}
		else
		{
			LogUtil::warning("Cannot execute update on database: " . mysql_error($this->resource) + "; SQL: " . $sql);
		}

		return false;
	}

	/**
	 * @param $sql SQL to execute
	 * @return LastInsertId
	 */
	function &executeUpdateAndReturn($sql)
	{
		if(SQLDEBUG)
		{
			LogUtil::debug("DB::executeUpdateAndReturn: SQL: " . $sql);
		}

		if($this->executeUpdate($sql))
		{
			$last_insert_id = $this->querySingle("SELECT LAST_INSERT_ID() as last_insert_id",
			new LastInsertIdFactory());

			return $last_insert_id;
		}

		LogUtil::warning("DAO::executeUpdateAndReturn: unable to get LAST_INSERT_ID from inserted record;\nSQL: " . $sql);
		return null;
	}

	protected function boolToInt($boolean)
	{
		return ($boolean) ? 1 : 0;
	}

	protected function intToBool($integer)
	{
		return ($integer == 1) ? true : false;
	}
}
?>