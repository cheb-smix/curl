<?php

namespace chebsmix\helpers\curl;

require_once("Curl.php");

$curl = new Curl();
$curl->setUrl('https://ipinfo.io/217.13.' . rand(0, 254) . '.' . rand(2, 254) . '/geo');
$curl->printDebug();

$curl = new Curl();
$curl->setUrl('https://api.nationalize.io/?name=dmitrii');
$curl->printDebug();

(new Curl())->parseCli("curl --location 'https://smix-soft.ru/api/v2/game/set-step' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'game_id=2' \
--data-urlencode 'from=1:1' \
--data-urlencode 'to=1:2' \
--data-urlencode 'effectivity=2' \
--data-urlencode 'mask=0101010010001010101001100110100101001010010101'")->printDebug();