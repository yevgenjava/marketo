<?php
require_once("common/app_config.inc");
$url = WebUtil::getCLParam("url");
// LogUtil::info("URL: " . $url . "\n");
$json_content = WebUtil::getRemoteUrlAsJson($url);

if(is_array($json_content) && sizeof($json_content) > 0)
{
	$jsonRecord = new JsonRecord();
	// echo "JSON Record: " . var_export($json_content, true);
	$jsonRecord->setId($json_content["id"]);
	$jsonRecord->setName($json_content["name"]);
	$jsonRecord->setValue($json_content["value"]);
	$jsonRecord->setTimestamp($json_content["timestamp"]);
	
	$jrDao = new JsonRecordDAO();
	$jrDao->createOrUpdate($jsonRecord);
	echo "1\n";
}
else
{
	LogUtil::error("Response from " . $url . " is not valid JSON");
	echo "-1\n";
}
?>