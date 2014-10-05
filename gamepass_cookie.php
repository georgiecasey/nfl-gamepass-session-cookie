<?php
require_once("database_details.php");
$mysql= new mysqli(DBSERVERHOST,DBUSERNAME,DBPASSWORD,DBNAME);
require_once("BaseScraper.php");
require_once "CookieJarWriter.inc.php";
include("GamePass.php");
$gamepass = new Gamepass("gamepass_cookiejar_file", $mysql);
// change this to your gamepass login
$gamepass->gamepassUsername="replace_with_your_gamepass_username";
$gamepass->gamepassPassword="replace_with_your_gamepass_password";

if ($gamepass->checkCookieStillWorks()===TRUE) {
    echo $gamepass->gamepass_jessionid;
    die();
} else if ($gamepass->checkCookieStillWorks()===FALSE) {
    $gamepass->loginGamepass();
    echo $gamepass->gamepass_jessionid;
}
?>
