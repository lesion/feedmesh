<?php
  include_once('SimplePie.compiled.php');
  header('Content-type: text/xml; charset=utf-8');

  $conf = include('conf.php');
  $feeds = file('./feeds');

  echo '<?xml version="1.0" encoding="UTF-8"?>';
  echo '<rss xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:foaf="http://xmlns.com/foaf/0.1/" xmlns:og="http://ogp.me/ns#" xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#" xmlns:schema="http://schema.org/" xmlns:sioc="http://rdfs.org/sioc/ns#" xmlns:sioct="http://rdfs.org/sioc/types#" xmlns:skos="http://www.w3.org/2004/02/skos/core#" xmlns:xsd="http://www.w3.org/2001/XMLSchema#" version="2.0">';
  echo '<channel>';
  echo "<title>{$conf['title']}</title>";
  echo "<link>{$conf['link']}</link>";
  echo "<description>{$conf['description']}</description>";
  echo "<language>{$conf['lang']}</language>";

  $feed = new SimplePie(); // Create a new instance of SimplePie

  $feed->set_feed_url($feeds);
  $feed->set_cache_duration (600);
  $success = $feed->init();

  foreach($feed->get_items(0, $conf['itemlimit']) as $item) {
    echo '<item>';
    echo '<pubDate>' . $item->get_gmdate() . '</pubDate>';
    echo '<title>[' . $item->get_feed()->get_title() . '] ' . $item->get_title() . '</title>';
    echo '<link>' . $item->get_permalink() . '</link>';
    echo '<description><![CDATA[' . $item->get_description() . ']]></description>';
    echo "</item>";
  }
  echo '</channel></rss>';
?>

