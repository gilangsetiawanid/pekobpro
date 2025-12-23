<?php
/**
 * DOODSLITE API - ENTERPRISE EDITION
 * Optimized for High Concurrency (100k+ Visitors)
 */

if (!ob_start("ob_gzhandler")) ob_start();

// KONFIGURASI
$API_KEY = "324754qf7ihkbj9wxlef7z"; // Ganti API Key Anda
$BASE_URL = "https://doodapi.co/api/";
$CACHE_DIR = __DIR__ . '/cache/';
$CACHE_TIME = 600; // 10 Menit

header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET");

// Layer 1: Browser Cache
$seconds_to_cache = $CACHE_TIME;
$ts = gmdate("D, d M Y H:i:s", time() + $seconds_to_cache) . " GMT";
header("Expires: $ts");
header("Pragma: cache");
header("Cache-Control: public, max-age=$seconds_to_cache");

if (!file_exists($CACHE_DIR)) { @mkdir($CACHE_DIR, 0755, true); }

$endpoint = $_GET['endpoint'] ?? '';
if (empty($endpoint)) {
    echo json_encode(["status" => 400, "msg" => "Endpoint required"]);
    exit;
}

// Generate Cache ID
$params = $_GET;
unset($params['endpoint'], $params['_']);
ksort($params);
$cache_key = md5($endpoint . http_build_query($params));
$cache_file = $CACHE_DIR . $cache_key . ".json";

// Layer 2: Server Cache
$is_cacheable = in_array($endpoint, ['folder/list', 'file/list', 'search/videos']);

if ($is_cacheable && file_exists($cache_file) && (time() - filemtime($cache_file) < $CACHE_TIME)) {
    $etag = md5_file($cache_file);
    header("Etag: $etag");
    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {
        header("HTTP/1.1 304 Not Modified"); exit;
    }
    $fp = fopen($cache_file, 'rb');
    fpassthru($fp);
    fclose($fp);
    exit;
}

// Layer 3: Live Fetch
$final_url = $BASE_URL . $endpoint . "?key=" . $API_KEY . "&" . http_build_query($params);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $final_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_TCP_FASTOPEN, 1);
curl_setopt($ch, CURLOPT_ENCODING, '');
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);

$result = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr = curl_error($ch);
curl_close($ch);

$data = json_decode($result, true);

// Emergency Fallback (Jika Doodstream Down)
if ($httpCode !== 200 || $curlErr || !isset($data['status']) || $data['status'] != 200) {
    if (file_exists($cache_file)) {
        $fp = fopen($cache_file, 'rb');
        fpassthru($fp);
        fclose($fp);
        exit;
    }
    http_response_code(502);
    echo json_encode(["status" => 502, "msg" => "Upstream Error"]);
    exit;
}

// Atomic Cache Write
if ($is_cacheable) {
    $temp_file = $cache_file . '.tmp';
    file_put_contents($temp_file, $result, LOCK_EX);
    rename($temp_file, $cache_file);
}

echo $result;
?>
