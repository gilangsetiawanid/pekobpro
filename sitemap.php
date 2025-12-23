<?php
// DOODSLITE SITEMAP - VERCEL EDITION
// Direct Fetch to Doodstream (Bypass Internal API to prevent timeout)

header("Content-Type: application/xml; charset=utf-8");
header("Cache-Control: public, s-maxage=3600"); // Cache sitemap 1 jam di CDN

// Konfigurasi
$API_KEY = "324754qf7ihkbj9wxlef7z"; 
$BASE_URL_SITE = "https://" . $_SERVER['HTTP_HOST']; // URL Website Vercel Anda

// Langsung tembak Doodstream (Jangan tembak api.php sendiri)
$dood_url = "https://doodapi.co/api/file/list?key=" . $API_KEY . "&per_page=100";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dood_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$json = curl_exec($ch);
curl_close($ch);

$data = json_decode($json, true);

echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';

// Halaman Utama
echo '<url><loc>' . $BASE_URL_SITE . '/</loc><changefreq>daily</changefreq><priority>1.0</priority></url>';

if (isset($data['result']['files'])) {
    foreach ($data['result']['files'] as $file) {
        // Slug SEO
        $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $file['title'])));
        $url = $BASE_URL_SITE . '/?v=' . $file['file_code'] . '&t=' . $slug;
        $title = htmlspecialchars($file['title']);
        $thumb = htmlspecialchars($file['single_img']);
        $date = substr($file['uploaded'], 0, 10);
        
        echo '<url>';
        echo '<loc>' . $url . '</loc>';
        echo '<lastmod>' . $date . '</lastmod>';
        echo '<changefreq>weekly</changefreq>';
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
?>
