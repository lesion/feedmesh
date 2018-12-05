# feedmesh
combines multiple feeds into a single rss feed.

you can specify your feed list inside `./feeds` and some
configuration inside `./conf.php`.


- it use [simplepie](https://simplepie.org) (included) to fetch specified
  feeds.
- supports xml and json format (using `format` parameter, default is xml)
- it use an hackish way to refresh feed asyncronously (see `refresh.php`)


Made with :heart: by [_TO*hacklab](https://autistici.org/underscore)

