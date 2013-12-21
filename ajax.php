<?php
/*
 * AJAX response php scrip I've used as test response
 * 
 */
$id = time() - 1000;
$ts = time();
$name = md5("Yevgen " . $id . $ts);
$value = "Yevgen $id $ts";
header("Content-type: application/json; encoding=UTF-8");

if(!isset($_REQUEST["s"]))
{
?>
{"id":<?= $id ?>, "name":"<?= $name ?>", "value":"<?= $value ?>", "timestamp":"<?= $ts ?>"}
<?
}
else
{
?>
{"id":1, "name":"Name Here", "value": "<?= $value ?>", "timestamp":"0"}
<?
}
?>

