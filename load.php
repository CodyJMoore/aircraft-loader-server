<?
function getUserIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

header("Access-Control-Allow-Origin: *");
error_reporting(E_ALL);
$id = $_GET["id"];
$definition = file_get_contents("gs://aircraft-bucket/" . $id . (isset($_GET["cockpit"]) ? "/cockpit.json" : "/aircraft.json"));
$descriptor = explode(PHP_EOL, file_get_contents("gs://aircraft-bucket/" . $id . "/descriptor.txt"));
$encoded = base64_encode($definition);

$result = new stdClass();
$result->id = intval($id);
$result->name = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $descriptor[0]);
$result->altId = intval(preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $descriptor[1]));
$result->fullPath = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $descriptor[2]);
$result->isPremium = 0;
$result->isCommunity = 0;
$result->definition = $encoded;

$json_result = json_encode($result);
echo $json_result;

file_put_contents("gs://aircraft-bucket/stats.csv", file_get_contents("gs://aircraft-bucket/stats.csv") . "\n" . getUserIP() . ", " . date(DATE_RFC2822));