<?php

if (!defined("WORKING_DIR")) die("No direct access allowed");

if (!function_exists('str_contains')) { // PHP <8
	function str_contains($haystack , $needle) {
		return strpos($haystack, $needle) !== false;
	}
}

function http_request($url, $timeout=null, $cookies=null, $header=null, $post_data=[], $is_jsonpost=false) {
	$ch = curl_init();
	$agent = "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.90 Safari/537.36";
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, $agent);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	if (is_array($header) && $header) curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	if (is_numeric($timeout) && $timeout) curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
	if (is_string($cookies) && $cookies) curl_setopt($ch, CURLOPT_COOKIE, $cookies);
	if ($post_data) {
		curl_setopt($ch, CURLOPT_POST, true);
		if (is_array($post_data)) {
			if ($is_jsonpost) {
				curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
			} else {
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
			}
		}
	}
	$output = curl_exec($ch);
	curl_close($ch);
	return $output;
}

function is_cli() {
	if (defined('STDIN')) return 'defined_stdin';
	if (function_exists('php_sapi_name') && php_sapi_name() == 'cli') return 'php_sapi_name';
	if ( empty($_SERVER['REMOTE_ADDR']) && !isset($_SERVER['HTTP_USER_AGENT']) && count($_SERVER['argv']) > 0) return '_server';
	return false;
}

function printd($txt, $tag="h3") {
	if (is_cli()) {
		echo "\n[] $txt";
	} else {
		echo "<$tag>$txt</$tag>\n";
	}
}

function disable_ob() {
	ini_set('output_buffering', 'off');
	ini_set('zlib.output_compression', false);
	ini_set('implicit_flush', true);
	ob_implicit_flush(true);
	while (ob_get_level() > 0) {
		$level = ob_get_level();
		ob_end_clean();
		if (ob_get_level() == $level) break;
	}
	if (function_exists('apache_setenv')) {
		apache_setenv('no-gzip', '1');
		apache_setenv('dont-vary', '1');
	}
}
