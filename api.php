<?php
/**
 * DOODSLITE API - VERCEL EDITION
 * Optimized for Serverless (No File Write)
 */

// Vercel handling
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET");

// Cache Header untuk Browser (Pengganti File Cache)
// Kita suruh Vercel Edge Network yang melakukan caching
header("Cache-Control: public, max-age=300, s-maxage=300, must-revalidate");

// KONFIGURASI
$API_KEY = "324754qf7ihkbj9wxlef7z"; // API KEY ANDA
$BASE_URL = "https://doodapi.co/api/";

$endpoint = $_GET['endpoint'] ?? '';

if (empty($endpoint)) {
    echo json_encode(["status" => 400, "msg" => "Endpoint required"]);
    exit;
}

// Persiapkan Parameter
$params = $_GET;
unset($params['endpoint']);
$query_string = http_build_query($params);
$final_url = $BASE_URL . $endpoint . "?key=" . $API_KEY . "&" . $query_string;

// Eksekusi CURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$result = curl_exec($ch);
curl_close($ch);

// Output langsung (Tanpa simpan ke file)
echo $result;
?>
