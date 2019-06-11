<?php
/**
 * this script combines multiple feeds into a single feed.
 * you can specify your feed list inside `./feeds` and some
 * configuration inside `./conf.php`.
 *
 * it use an hackish way to refresh feed asyncronously (see `refresh.php`)
 **/

  $conf = include('conf.php');
  header('Access-Control-Allow-Origin: *');

  $format = isset($_GET['format']) ? $_GET['format'] : 'xml';
  $last_refresh = 0;
  $files = [
    "xml"  => [ "name" => "feed.xml", "type" => "text/xml" ],
    "json" => [ "name" => "feed.json", "type" => "application/json" ]
  ];

  $cache_file = $files[$format]['name'];
  $content_type = $files[$format]['type'];

  header("Content-type: $content_type; charset=utf-8");
  if (file_exists($cache_file)) {
    readfile($cache_file);
    $last_refresh = time()-stat($cache_file)['mtime'];
  }

  // refresh feeds if cache expired or absent
  if ($last_refresh>$conf['cache_duration']/2 or $last_refresh===0) {

    // call refresh script via curl
    // using a low timeout
    $ch = curl_init();
    $path = dirname($_SERVER['PHP_SELF']);
    if($path === '/') $path = '';
    curl_setopt($ch, CURLOPT_URL, "http://{$_SERVER['HTTP_HOST']}$path/refresh.php");
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 100);
    curl_exec($ch);
    sleep(0.5);
    curl_close($ch);
  }

?>

