<?php

namespace chebsmix\helpers\curl;

require_once("Curl.php");

$curl = new Curl();
$curl->setUrl('https://ipinfo.io/217.13.' . rand(0, 254) . '.' . rand(2, 254) . '/geo');
$curl->printDebug();

$curl = new Curl();
$curl->setUrl('https://api.nationalize.io/?name=dmitrii');
$curl->printDebug();

(new Curl())->parseCli("curl --location --request GET 'https://ipinfo.io/147.83." . rand(0, 254) . "." . rand(2, 254) . "/geo'")->printDebug();