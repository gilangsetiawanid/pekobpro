<?php
// DOODSLITE SITEMAP - GOOGLE BOT FRIENDLY
ob_start();
header("Content-Type: application/xml; charset=utf-8");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache"); header("Expires: 0");

$PROTOCOL = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$BASE_URL = $PROTOCOL . "://" . $_SERVER['HTTP_HOST'];
$json = @file_get_contents($BASE_URL . "/api.php?endpoint=file/list&per_page=100");
$data = json_decode($json, true);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

echo '<url><loc>' . $BASE_URL . '/</loc><changefreq>hourly</changefreq><priority>1.0</priority></url>';

if (isset($data['result']['files'])) {
    foreach ($data['result']['files'] as $file) {
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $file['title'])));
        $url = $BASE_URL . '/?v=' . $file['file_code'] . '&t=' . $slug;
        $title = htmlspecialchars($file['title']);
        $thumb = htmlspecialchars($file['single_img']);
        $date = substr($file['uploaded'], 0, 10);
        
        echo '<url>';
        echo '<loc>' . $url . '</loc>';
        echo '<lastmod>' . $date . '</lastmod>';
        echo '<changefreq>daily</changefreq>';
        echo '<priority>0.8</priority>';
        echo '<video:video>';
        echo '<video:thumbnail_loc>' . $thumb . '</video:thumbnail_loc>';
        echo '<video:title>' . $title . '</video:title>';
        echo '<video:description>Nonton ' . $title . '</video:description>';
        echo '<video:player_loc autoplay="ap=1">https://dood.li/e/' . $file['file_code'] . '</video:player_loc>';
        echo '<video:publication_date>' . $date . '</video:publication_date>';
        echo '</video:video>';
        echo '</url>';
    }
}
echo '</urlset>';
ob_end_flush();
?>
