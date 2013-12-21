<?php
/**
 * Some logging methods.
 *
 * @author yevgen.java@gmail.com
 */
class LogUtil
{
	/**
	 * Logs error condition and terminates current PHP process.
	 * 
	 * @param string $string
	 */
	public static function error($string)
	{
		if(defined("LOG_STDOUT"))
		{
			echo "!!! ERROR: " . $string . "\n";
			return;
		}
		exit;
	}

	/**
	 * Logs warning message.
	 * 
	 * @param string $string
	 */
	public static function warning($string)
	{
		if(defined("LOG_STDOUT") && LOG_STDOUT == true)
		{
			echo "Warning: " . $string . "\n";
			return;
		}
	}

	/**
	 * Prints debugging information.
	 * 
	 * @param string $string
	 */
	public static function debug($string)
	{
		echo "* DEBUG * : " . $string . "\n";
	}

	/**
	 * Prints information messages to STDOUT.
	 * 
	 * @param string $string
	 */
	public static function info($string)
	{
		if(defined("LOG_STDOUT"))
		{
			echo "INFO: " . $string . "\n";
			return;
		}

		// mail("yevgen.java@gmail.com", "Log Info", $string);
	}
}
?>
