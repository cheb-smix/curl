<?php

namespace chebsmix\helpers\curl;

require_once("Curl.php");

$curl = new Curl();

$curl->setUrl('https://ipinfo.io/217.13.124.105/geo');

$curl->printDebug();

$curl = new Curl();

$curl->setUrl('https://api.nationalize.io/?name=dmitrii');

$curl->printDebug();