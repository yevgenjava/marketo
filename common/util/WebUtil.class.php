<?php
/**
 * Provides common Web Utility methods.
 * @author jgolubenko@yahoo.com
 */
class WebUtil
{
	/**
	 * Returns parameter from request or default value if parameter is not present or blank.
	 *
	 * @param mixed $name name of parameter to get.
	 * @param mixed $default default value to return if param is not present (null) or blank.
	 * @return string value for parameter or default value.
	 */
	public static function getParam($name, $default = null)
	{
		if(self::isValidParam($name))
		{
			return $_REQUEST[$name];
		}

		return $default;
	}

	/**
	 * Returns command line parameter value for given parameter name.
	 *
	 * @param string $name name of the parameter for which value should be returned.
	 * @param mixed $default default value to return if no paramter is present or it's blank.
	 */
	public static function getCLParam($name, $default = null)
	{
		foreach($_SERVER["argv"] as $argument)
		{
			if(preg_match_all("/(\w+)=(.*[^\s+])/", $argument, $matches))
			{
				if($matches[1][0] == $name)
				{
					// echo "Matched: " . var_export($matches, true) . "\n";
					return $matches[2][0];
				}
			}
		}
		
		// If no default, return first parameter
		if($default == null)
		{
			return $_SERVER["argv"][1];			
		}
		
		return $default;
	}

	/**
	 * Returns file object for given parameter name, or default value (null)
	 *
	 *
	 * @param string $name file name for which file object needs to be returned.
	 * @param object $default default value to return if file not found in the request.
	 */
	public static function getFile($name, $default = null)
	{
		if($_FILES[$name] != null)
		{
			return $_FILES[$name];
		}
			
		return $default;
	}

	/**
	 * Reads remote url as json.
	 *
	 * in php.ini we should have:
	 * allow_url_fopen = 1
	 *
	 * @param string $url remote URL.
	 * @return array associative array of key=>val from json.
	 */
	public static function getRemoteUrlAsJson($url)
	{
		if(!ini_get("allow_url_fopen") || ini_get("allow_url_fopen") != 1)
		{
			LogUtil::error("Error: allow_url_fopen should be equals to 1/TRUE in php.ini file");
			exit;	
		}
		
		$json = file_get_contents($url);

		$safe_json = str_replace("\n", "\\n", $json);

		$object = json_decode($json);

		switch (json_last_error())
		{
			case JSON_ERROR_NONE:
				// LogUtil::info("Decoded JSON from remote URL: " . $url . ": " . var_export($object, true));
				break;
			case JSON_ERROR_DEPTH:
				LogUtil::error("Cannot decoded JSON from URL: " . $url .
					" - Maximum stack depth exceeded");
				break;
			case JSON_ERROR_STATE_MISMATCH:
				LogUtil::error("Cannot decoded JSON from URL: " . $url .
					" - Underflow or the modes mismatch");
				break;
			case JSON_ERROR_CTRL_CHAR:
				LogUtil::error("Cannot decoded JSON from URL: " . $url .
					" - Unexpected control character found");
				break;
			case JSON_ERROR_SYNTAX:
				LogUtil::error("Cannot decoded JSON from URL: " . $url .
					" - Syntax error, malformed JSON");
				break;
			case JSON_ERROR_UTF8:
				LogUtil::error("Cannot decoded JSON from URL: " . $url .
					" - Malformed UTF-8 characters, possibly incorrectly encoded");
				break;
			default:
				LogUtil::error("Cannot decoded JSON from URL: " . $url . " - Unknown error");
				break;
		}
		$object = (array)$object;
		// LogUtil::info("Returning json object: " . var_export($object,true));
		return $object;
	}

	/**
	 * Checks if request parameter with given name is present (isset) and
	 * not empty (!empty)
	 *
	 * @param string $name parameter name to check.
	 * @return boolean true if valid and meets described condition, false otherwise.
	 */
	public static function isValidParam($name)
	{
		return (isset($_REQUEST[$name]) && !empty($_REQUEST[$name]));
	}
}
?>
