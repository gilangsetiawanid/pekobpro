<?php
header("Content-Type: application/xml; charset=utf-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
echo '<url><loc>https://'.$_SERVER['HTTP_HOST'].'/</loc><priority>1.0</priority></url>';
echo '</urlset>';
?>