<?
header("Content-Type: application/octet-stream");
header("Access-Control-Allow-Origin: *");
$name = substr($_GET["name"], 0, strpos($_GET["name"], "?"));
readfile("gs://aircraft-bucket/" . $name);