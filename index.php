<?php
  include_once('SimplePie.compiled.php');
  header('Content-type: text/xml; charset=utf-8');
  header('Access-Control-Allow-Origin: *');

  $conf = include('conf.php');

  // send res
  if (file_exists('cache/feed.xml')) {
    readfile('cache/feed.xml');
    flush();
  }

  $feeds = file('./feeds');

  $rss = '<?xml version="1.0" encoding="UTF-8"?><rss version="2.0"><channel>';
  $rss .= "<title>{$conf['title']}</title>";
  $rss .= "<link>{$conf['link']}</link>";
  $rss .= "<description>{$conf['description']}</description>";
  $rss .= "<language>{$conf['lang']}</language>";

  $feed = new SimplePie();
  $feed->set_feed_url($feeds);
  $feed->set_cache_duration (3600);
  $feed->init();
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
?>

