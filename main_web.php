<?php

// YOU CAN CHANGE THIS:

define("COOKIE_FILE_NAME", 'cookie_0.txt');
// see on 'data' folder.
// If you want multi-login, use another file name;
// Otherwise, same-login just use same file name.

define("SELECTED_GAME", 'genshin');
// genshin = Genshin Impact
// honkai = Honkai Impact 3

define("SELECTED_LANGUAGE_FILE", 'en');
// see on 'lang' folder.
// file name without extension (without '.json')
// en = English
// id = Indonesia

// --------------------

// === DON'T CHANGE LINES BELOW !!! ===
define("INDEX_NAME", basename(__FILE__));
define("WORKING_DIR", __DIR__);
include __DIR__ . "/lib/_web.php";