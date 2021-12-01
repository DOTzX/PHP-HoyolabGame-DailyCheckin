<?php
// =======================================================================
// Author: https://github.com/DOTzX
// Source: https://github.com/DOTzX/PHP-HoyolabGame-DailyCheckin
// =======================================================================

if (!defined("WORKING_DIR")) die("No direct access allowed");
if (!defined("SELECTED_GAME")) die("No direct access allowed");

if (SELECTED_GAME == "genshin") {
	$_nq = http_build_query([
		"lang" => "en-us",
		"act_id" => "e202102251931481",
	]);
	
	$link_listitem_of_month = "https://hk4e-api-os.mihoyo.com/event/sol/home?" . $_nq;
	$link_current_signin_info = "https://hk4e-api-os.mihoyo.com/event/sol/info?" . $_nq;
	$link_signin = "https://hk4e-api-os.mihoyo.com/event/sol/sign?" . $_nq;
	
	define('GAME_EVENTPAGE', "https://webstatic-sea.mihoyo.com/ys/event/signin-sea/index.html?" . $_nq);
} else if (SELECTED_GAME == "honkai") {
	$_nq = http_build_query([
		"lang" => "en-us",
		"act_id" => "e202110291205111",
	]);
	
	$link_listitem_of_month = "https://api-os-takumi.mihoyo.com/event/mani/home?" . $_nq;
	$link_current_signin_info = "https://api-os-takumi.mihoyo.com/event/mani/info?" . $_nq;
	$link_signin = "https://api-os-takumi.mihoyo.com/event/mani/sign?" . $_nq;
	
	define('GAME_EVENTPAGE', "https://webstatic-sea.mihoyo.com/bbs/event/signin-bh3/index.html?" . $_nq);
} else {
	die("'". SELECTED_GAME . "' is not available");
}

define('HOYOLAB_HEADER', [
	'Accept: application/json, text/plain, */*',
	'Accept-Language: en-US;q=0.8,en;q=0.7',
	'Connection: keep-alive',
	'Origin: https://webstatic-sea.mihoyo.com',
	'Referer: ' . GAME_EVENTPAGE,
	'Cache-Control: max-age=0',
]);

function getListItem() {
	global $link_listitem_of_month;
	return json_decode(http_request($link_listitem_of_month, null, HOYOLAB_COOKIES, HOYOLAB_HEADER), true);
}

function getSigninInfo() {
	global $link_current_signin_info;
	return json_decode(http_request($link_current_signin_info, null, HOYOLAB_COOKIES, HOYOLAB_HEADER), true);
}

function sendSignin() {
	global $link_signin;
	$_header = array_values(HOYOLAB_HEADER);
	array_push($_header, "Content-Type: application/json;charset=utf-8");
	return json_decode(http_request($link_signin, null, HOYOLAB_COOKIES, $_header, [
		"act_id" => "e202102251931481",
	], true), true);
}

function check_compatability_cookies($txt) {
	return $txt && str_contains($txt, "ltoken=") &&
		str_contains($txt, "ltuid=");
		// str_contains($txt, "cookie_token=")
		// str_contains($txt, "account_id=")
		// str_contains($txt, "login_ticket=")
}

function check_language_compatability($defined_lang) {
	$sfo_language = new SimpleFileOpener("lang/id.json");
	$language = json_decode($sfo_language->read(), true);
	foreach ($language as $key => $value) {
		if (!array_key_exists($key, $defined_lang)) {
			die("This language[key] isn't registered: " . $key);
		}
	}
	foreach ($defined_lang as $key => $value) {
		if (!array_key_exists($key, $language)) {
			die("This language[key] not really used again: " . $key);
		}
	}
}
