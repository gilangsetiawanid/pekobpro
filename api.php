<?php
// DOODSLITE API - VERCEL SERVERLESS EDITION
// Optimized for Vercel Edge Network

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");

// --- VERCEL CACHING MAGIC ---
// s-maxage=600 : Cache di server Vercel (CDN) selama 10 menit.
// stale-while-revalidate=30 : Jika cache expired, server tetap kirim data lama sambil update di background.
header("Cache-Control: public, s-maxage=600, stale-while-revalidate=30");

// KONFIGURASI
$API_KEY = "324754qf7ihkbj9wxlef7z"; // Ganti API Key Anda
$BASE_URL = "https://doodapi.co/api/";

$endpoint = $_GET['endpoint'] ?? '';

if (empty($endpoint)) {
    echo json_encode(["status" => 400, "msg" => "Endpoint required"]);
    exit;
}

// FETCH DATA DARI DOODSTREAM
$params = $_GET;
unset($params['endpoint']);
$query_string = http_build_query($params);
$final_url = $BASE_URL . $endpoint . "?key=" . $API_KEY . "&" . $query_string;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 15); // Timeout serverless function
$result = curl_exec($ch);
curl_close($ch);

// Output langsung
echo $result;
?>
