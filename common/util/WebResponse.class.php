<?php
/**
 * Generates JSON responses.
 */
class WebResponse
{
	/**
	 * Outputs JSON encoded response:
	 * {"success" : true, "message" : <message>} and exits application.
	 *
	 * @param string $message message to output.
	 */
    public static function success($message)
    {
        $data = array("success" => true, "message" => $message);
        echo json_encode($data);
        exit;
    }

    /**
	 * Outputs JSON encoded response:
	 * {"success" : false, "message" : <message>} and exits application.
	 *
	 * @param string $message message to output.
	 */
    public static function fail($message)
    {
        $data = array("success" => false, "message" => $message);
        echo json_encode($data);
        exit;
    }

    /**
	 * Outputs JSON encoded response.
	 *
	 * @param array $data associative array of data to json-encode
	 * @param boolean $exit if true (default) will exit application after the output.
	 */
    public static function respond($data, $exit = true)
    {
        echo json_encode($data);
        if($exit) { exit; }
    }
}
?>
