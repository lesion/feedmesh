<?php

/**
   this script is called from `index.php` via curl
   and it's an hack to do async operation in PHP
   usually you can do the following....

   ignore_user_abort(TRUE);
   set_time_limit(0);
   header("Status: 200\r\n", true);
   header("Content-Length: 0\r\n", true);
   header("Connection: Close\r\n", true);
   ob_end_flush();
   flush();

   to close connection and proceed with your long operation
   but using mod_fcgid this is not possibile and you need to use
   register_shutdown_function (called also when client disconnect)
   and disconnect the client using a small timeout (see index.php)
**/

  function run_me_on_disconnect() {
    // needed because register_shutdown_function changes 
    // current working directory to ServerRoot
    chdir(__DIR__);
    $conf = include('conf.php');

    include_once('SimplePie.compiled.php');

    // list of feeds to merge...
    $feeds = file('./feeds');
    $rss = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>';
    $rss .= "<title>{$conf['title']}</title>";
    $rss .= "<link>{$conf['link']}</link>";
    $rss .= "<description>{$conf['description']}</description>";
    $rss .= "<language>{$conf['lang']}</language>";

    $feed = new SimplePie();
    $feed->set_feed_url($feeds);
    $feed->set_cache_duration($conf['cache_duration']);
    $feed->init();
    if ($feed->error()) {
      echo $feed->error();
    }
    $items = [];
    foreach($feed->get_items(0, $conf['itemlimit']) as $item) {
      $itemObj = [ "title" => $item->get_title(), "date" => $item->get_date("d M y"),
        "feed_title" => $item->get_feed()->get_title(), "link" => $item->get_permalink() ];
      array_push($items, $itemObj);
      $rss .= '<item><pubDate>' . $item->get_date() . '</pubDate>';
      $rss .= '<title>[' . $item->get_feed()->get_title() . '] ' . $item->get_title() . '</title>';
      $rss .= '<link>' . $item->get_permalink() . '</link>';
      $rss .= '<description><![CDATA[' . $item->get_description() . ']]></description></item>';
    }
    $rss .= '</channel></rss>';
    file_put_contents('feed.xml', $rss);
    file_put_contents('feed.json', json_encode($items));
 }

if(php_sapi_name() == "cli") {
    run_me_on_disconnect();
} else {
    register_shutdown_function('run_me_on_disconnect');
}

?>
