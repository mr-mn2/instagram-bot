<?php
include "bootstrap/config.php";

$url = 'https://9ab9-185-245-85-110.ngrok-free.app' . '/Bots/insta/bot.php';

include "vendor/autoload.php";
$bot = new \App\Telegram\telegramBot(BOT_TOKEN);
var_dump( $bot -> setWebhook(preg_replace('/\s+/', '', $url)));
