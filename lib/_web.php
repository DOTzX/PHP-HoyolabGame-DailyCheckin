<?php
// === DON'T CHANGE LINES BELOW !!! ===
// =======================================================================
// Author: https://github.com/DOTzX
// Source: https://github.com/DOTzX/PHP-HoyolabGame-DailyCheckin
// =======================================================================

if (!defined("INDEX_NAME")) die("No direct access allowed");

if (!defined("SELECTED_LANGUAGE_FILE")) die("No direct access allowed");
if (!defined("COOKIE_FILE_NAME")) die("No direct access allowed");

include WORKING_DIR . "/lib/class.DOTzX.SimpleFileOpener.php"; // Find new update here: https://gist.github.com/DOTzX/26afe5ab070acf09e4f055db37a0ad97
include WORKING_DIR . "/lib/general.php";
include WORKING_DIR . "/lib/game.php";

$sfo_language = new SimpleFileOpener("lang/". SELECTED_LANGUAGE_FILE .".json");
$language = json_decode($sfo_language->read(), true);
$language = $language ? $language : [];
check_language_compatability($language);
// === DON'T CHANGE LINES ABOVE !!! ===

if (! is_cli()) {
    disable_ob();
} else {
    die("Please run from main_cli.php");
}

// =======================================================================

printd("[CONFIG] Used cookies name: data/" . COOKIE_FILE_NAME);
printd("[CONFIG] Selected game name: " . SELECTED_GAME);
printd("[CONFIG] Selected language: " . SELECTED_LANGUAGE_FILE);

$sfo_cookie = new SimpleFileOpener("data/" . COOKIE_FILE_NAME);

if (isset($_POST["_data"])) {
	if (check_compatability_cookies($_POST["_data"])) {
		$sfo_cookie->write($_POST["_data"]);
		define("HOYOLAB_COOKIES", $_POST["_data"]);
		printd($language["SUCCESS_SAVE_DATA"], "h1");
	} else {
		printd($language["INVALID_RECEIVED_DATA"], "h1");
	}
} else {
	$cookie = $sfo_cookie->read();
	if (!check_compatability_cookies($cookie)) $cookie = "";
	define("HOYOLAB_COOKIES", $cookie);
}

// =======================================================================

$fcontent = "";
if (file_exists(WORKING_DIR . "/REPOSITORY_LAST_UPDATE")) {
	$sfo = new SimpleFileOpener("REPOSITORY_LAST_UPDATE");
	$fcontent = $sfo->read();
}
$rlu_check = http_request("https://raw.githubusercontent.com/DOTzX/PHP-HoyolabGame-DailyCheckin/master/REPOSITORY_LAST_UPDATE?t=" . time(), 5);
$new_update = false;
if ($rlu_check) {
	if ($rlu_check == "404: Not Found") {
		$new_update = "https://github.com/DOTzX/PHP-HoyolabGame-DailyCheckin";
	} else if ($fcontent != $rlu_check) {
		$_exp = explode("|", $rlu_check, 2);
		if (count($_exp) == 2) {
			$new_update = $_exp[1];
		} else {
			$new_update = "https://github.com/DOTzX/PHP-HoyolabGame-DailyCheckin";
		}
	}
	if ($new_update) {
?>

<!-- oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo -->
<h1><?= str_replace("[new_update]", $new_update, $language["NEW_UPDATE_HTML"]) ?></h1>
<!-- oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo -->

<?php
		die();
	}
}

// =======================================================================

if (!HOYOLAB_COOKIES) {
?>

<!-- oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo -->
<div><?=str_replace("[game_eventpage]", GAME_EVENTPAGE, $language["TUTORIAL_STEP_1_HTML"])?></div>
<div><?=$language["TUTORIAL_STEP_2_HTML"]?></div>
<div><?=$language["TUTORIAL_STEP_3_HTML"]?></div>
<div><?=$language["TUTORIAL_STEP_4_HTML"]?></div>
<div><?=$language["TUTORIAL_STEP_5_HTML"]?></div>
<div>
	<form method="POST" action="<?=INDEX_NAME?>">
		<textarea name="_data" placeholder="<?=htmlentities($language["TUTORIAL_STEP_5_HTML_PLACEHOLDER"])?>" style="width:100%; height:20vh"></textarea>
		<input type="submit" />
	</form>
</div>
<!-- oooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooo -->

<?php
	die();
} else {
	printd($language["SUCCESS_LOAD_DATA"], "h1");
    printd(str_replace("[cookie_file_name]", COOKIE_FILE_NAME, $language["NOTICE_IF_FAIL_COOKIES_HTML"]), "h2");
}

// =======================================================================

printd($language["DAILY_STATUS_CHECKING"]);

$sign_info = getSigninInfo();

if ($sign_info["data"]) {
	printd(str_replace("[total_sign_day]", $sign_info["data"]["total_sign_day"], $language["DAILY_STATUS_CHECKIN_DAY"]));
	printd(str_replace("[today_date]", $sign_info["data"]["today"], $language["DAILY_STATUS_TODAY_DATE"]));
	printd($language["DAILY_STATUS_TEXT"] . " " . ($sign_info["data"]["is_sign"] ? $language["DAILY_STATUS_ALREADY"] : $language["DAILY_STATUS_NOTYET"]), "h1");
} else {
	printd($language["DAILY_STATUS_TEXT"] . " " . $sign_info["message"]);
	die();
}

if ($sign_info["data"]["is_sign"]) die();

printd($language["DAILY_CHECKING_ON"]);

$signin_send_status = sendSignin();
printd($language["DAILY_CHECKING_STATUS"] . " " . $signin_send_status["message"], "h1");

$item_list = getListItem();

if ($item_list["data"] && isset($item_list["data"]["awards"])) {
	$total_sign_day_index = $sign_info["data"]["total_sign_day"];
	if (isset($item_list["data"]["awards"][$total_sign_day_index])) {
		$today_item = $item_list["data"]["awards"][$total_sign_day_index];
		echo "\n<hr>\n";
		$item_obtained = $today_item["cnt"] . " " . $today_item["name"];
		printd(str_replace("[today_item]", $item_obtained, $language["DAILY_TODAY_ITEM"]), "h1");
		echo "\n<hr>\n";
	}
}

printd($language["DAILY_CHECKING_DONE/FINALPROCESS"]);
