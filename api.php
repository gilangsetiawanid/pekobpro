<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$API_KEY = "324754qf7ihkbj9wxlef7z";
$BASE_URL = "https://doodapi.co/api/";
$endpoint = $_GET['endpoint'] ?? '';

if (empty($endpoint)) {
    echo json_encode(["status" => 400, "msg" => "Endpoint required"]);
    exit;
}

$params = $_GET;
unset($params['endpoint']);
$query_string = http_build_query($params);
$final_url = $BASE_URL . $endpoint . "?key=" . $API_KEY . "&" . $query_string;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$result = curl_exec($ch);
curl_close($ch);

echo $result;
?>